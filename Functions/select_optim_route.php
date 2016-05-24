<?php
$optim = new Query($conn, "
SELECT t.type, s.lat, s.lang, s.locationname, ROUND(1000 * 111.1111 *
    DEGREES(ACOS(COS(RADIANS(o.orilat))
         * COS(RADIANS(o.destlat))
         * COS(RADIANS(o.orilong - o.destlong))
         + SIN(RADIANS(o.orilat))
         * SIN(RADIANS(o.destlat)))),2) AS distance_in_m
FROM `trips_has_stops` t
join `stops` s on s.idstop = t.stops_stopsid
join `orders` o on t.trips_tripsid = o.orderid
join `reports_has_orders` r on o.orderid = r.orders_ordersid
where r.reports_reportsid = {$reportid}
order by distance_in_m asc, t.trips_tripsid asc, t.type desc;");
$optim->exec("");
