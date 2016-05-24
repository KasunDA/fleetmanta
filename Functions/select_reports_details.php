<?php
$orders = new Query($conn, "
SELECT o.orderid, o.containers_containerid, o.quantity, t.description, o.trucks_idtruck, ROUND(1000 * 111.1111 *
    DEGREES(ACOS(COS(RADIANS(o.orilat))
         * COS(RADIANS(o.destlat))
         * COS(RADIANS(o.orilong - o.destlong))
         + SIN(RADIANS(o.orilat))
         * SIN(RADIANS(o.destlat)))),2) AS distance_in_m,
         ROUND(u.costkm/1000*ROUND(1000 * 111.1111 *
    DEGREES(ACOS(COS(RADIANS(o.orilat))
         * COS(RADIANS(o.destlat))
         * COS(RADIANS(o.orilong - o.destlong))
         + SIN(RADIANS(o.orilat))
         * SIN(RADIANS(o.destlat)))),2),2) cost FROM `reports_has_orders` r
join `orders` o on r.orders_ordersid = o.orderid
join `trips` t on o.trips_tripsid = t.idtrips
join `trucks` u on o.trucks_idtruck = u.idtrucks
WHERE r.reports_reportsid='{$reportid}'
ORDER BY distance_in_m");
$orders->exec("");
