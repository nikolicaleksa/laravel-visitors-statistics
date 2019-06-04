<?php

Route::get('/statistics/{year}/{month?}', 'StatisticsController@getStatistics')
    ->where([
        'year' => '\d{4}',
        'month' => '\d{1,2}'
    ])
    ->name('getStatistics');

Route::get('/statistics/unique/{year}/{month?}', 'StatisticsController@getUniqueStatistics')
    ->where([
        'year' => '\d{4}',
        'month' => '\d{1,2}'
    ])
    ->name('getUniqueStatistics');

Route::get('/statistics/countries', 'StatisticsController@getCountriesStatistics')
    ->name('getCountriesStatistics');

Route::get('/statistics/available/{year?}', 'StatisticsController@getAvailableDates')
    ->where([
        'year' => '\d{4}'
    ])
    ->name('getAvailableDates');
