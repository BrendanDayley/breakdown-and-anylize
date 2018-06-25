SELECT 
    SUM( 
        ( SELECT COUNT(n.id)
        FROM CIL_Notes n
        WHERE n.Ref_id=i.id
                AND n.serviceReceived=1
                AND n.date>='2018-01-01'
                AND n.date<='2018-06-12'
                AND n.date>=cs.date
                AND (n.date<=cs.endDate
                OR cs.endDate IS NULL)
                AND n.deleted=0)
    ) AS notecnt,
    COUNT( DISTINCT i.Consumers_id) AS distIlogcnt
    SUM(         
        (SELECT 
            COUNT( DISTINCT n.Consumers_id )
        FROM CIL_Notes n
        WHERE n.Ref_id=i.id 
                AND n.Consumers_id != i.Consumers_id 
                AND n.serviceReceived=1
                AND n.date>='2018-01-01'
                AND n.date<='2018-06-12'
                AND n.date>=cs.date
                AND (n.date<=cs.endDate
                OR cs.endDate IS NULL)
                AND n.deleted=0)
    ) AS distNotecnt,
    st.type,
    st.id AS serviceId
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
        HAVING cs.date<='2018-06-12'
                AND (endDate IS NULL
                OR endDate>='2018-01-01')) cs
        ON (cs.Consumers_id=c.id)
WHERE i.deleted = 0
        AND c.deleted = 0
        AND c.county_id = '2783'
        AND i.is_consumer IN (1)
        AND i.ServiceTypes_id IN (40)
        AND ( i.Consumers_id IS NOT NULL
        AND i.Consumers_id > 0 )
GROUP BY  c.id 