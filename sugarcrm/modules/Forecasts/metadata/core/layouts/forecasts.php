<?php
$viewdefs['Forecasts']['core']['layout']['forecasts'] = array(
    'type' => 'forecasts',
    'components' => array(
        0 => array(
            'view' => 'forecastsFilter',
            'model' => array(
                'module' => 'Forecasts',
                'name' => 'Filters'
            )
        ),
        1 => array(
            'view' => 'chartOptions',
            'model' => array(
                'module' => 'Forecasts',
                'name' => 'ChartOptions'
            )
        ),
        2 => array(
            'view' => 'tree',
        ),
        3 => array(
            'view' => 'chart',
        ),
        4 => array(
            'view' => 'progress',
        ),
        5 => array(
            'view' => 'forecastsCommitted',
            'model' => array(
                'module' => 'Forecasts',
                'name' => 'Committed'
            ),
        ),
        6 => array(
            'view' => 'forecastsWorksheet',
        ),
        7 => array(
            'view' => 'forecastsSubnav',
        )
    )
);