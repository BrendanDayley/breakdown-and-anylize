SELECT COUNT(i.id) AS cnt,
         st.type,
         st.id AS serviceId ,
         IF((YEAR('2020-06-08') - DATE_FORMAT(c.DOB,'%Y') - (DATE_FORMAT('2020-06-08','00-%m-%d') < DATE_FORMAT(c.DOB,'00-%m-%d')))>=1
        AND (YEAR('2020-06-08') - DATE_FORMAT(c.DOB,'%Y') - (DATE_FORMAT('2020-06-08','00-%m-%d') < DATE_FORMAT(c.DOB,'00-%m-%d')))<=5000, '1_5000','Undefined') AS Age
FROM CIL_IRLog AS i
INNER JOIN CIL_ServiceTypes AS st
    ON ( i.ServiceTypes_id = st.id )
INNER JOIN CIL_Consumers AS c
    ON ( c.id = i.Consumers_id )
INNER JOIN 
    (SELECT cs.*,
         
        (SELECT `date`
        FROM CIL_ConsumerStatus
        WHERE deleted=0
                AND StatusTypes_id!=1
                AND `date`>=cs.date
                AND is_consumer=cs.is_consumer
                AND Consumers_id=cs.Consumers_id
        ORDER BY  `date` ASC LIMIT 1) AS endDate
        FROM CIL_ConsumerStatus cs
        WHERE cs.deleted=0
                AND cs.StatusTypes_id=1
                AND cs.is_consumer IN (1)
        HAVING cs.date<='2020-06-08'
                AND (endDate IS NULL
                OR endDate>='2018-06-01')) cs
        ON (cs.Consumers_id=c.id)
WHERE i.deleted = 0
        AND c.deleted = 0
        AND c.county_id IN (841,95,2774,2775,1382,2777,856,1898,2778,2779,2780,332,864,2783,1045,867,2785,2786,176,203,873,874,2788,310,2789,2791,312,2798,3225,2799,2800)
        AND i.is_consumer IN (1)
        AND i.ServiceTypes_id IN (27)
        AND ((i.IRLogStatus_id=2
        AND i.statusDate >= '2018-06-01'
        AND i.statusDate <= '2020-06-08'
        AND i.statusDate>=cs.date
        AND (i.statusDate<=cs.endDate
        OR cs.endDate IS NULL))
        OR (
    (SELECT COUNT(n.id) AS cnt
    FROM CIL_Notes n
    WHERE n.Ref_id=i.id
            AND n.serviceReceived=1
            AND n.date>='2018-06-01'
            AND n.date<='2020-06-08'
            AND n.date>=cs.date
            AND (n.date<=cs.endDate
            OR cs.endDate IS NULL)
            AND n.deleted=0)>0))
        AND ( i.Consumers_id IS NOT NULL
        AND i.Consumers_id > 0 )
GROUP BY  c.id