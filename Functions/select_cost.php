<?php

$totalcost = new Query($conn,"
SELECT SUM(
ROUND(u.costkm * 111.1111
* DEGREES(ACOS(COS(RADIANS(o.orilat))
* COS(RADIANS(o.destlat))
* COS(RADIANS(o.orilong - o.destlong)) + SIN(RADIANS(o.orilat))
* SIN(RADIANS(o.destlat)))),2))
AS cost from `reports_has_orders` r join `orders` o on r.orders_ordersid = o.orderid join `trips` t on o.trips_tripsid = t.idtrips join `trucks` u on o.trucks_idtruck = u.idtrucks WHERE r.reports_reportsid={$reportid};");
$totalcost->exec("");
