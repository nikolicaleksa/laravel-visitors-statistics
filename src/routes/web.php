<?php

Route::get('/statistics/{year}/{month?}', 'StatisticsController@getStatistics')
    ->where([
        'year' => '\d{4}',
        'month' => '\d{1,2}'
    ])
    ->name('visitorstatistics.all_statistics');

Route::get('/statistics/unique/{year}/{month?}', 'StatisticsController@getUniqueStatistics')
    ->where([
        'year' => '\d{4}',
        'month' => '\d{1,2}'
    ])
    ->name('visitorstatistics.unique_statistics');

Route::get('/statistics/total/{year}/{month?}', 'StatisticsController@getTotalStatistics')
    ->where([
        'year' => '\d{4}',
        'month' => '\d{1,2}'
    ])
    ->name('visitorstatistics.total_statistics');

Route::get('/statistics/countries', 'StatisticsController@getCountriesStatistics')
    ->name('visitorstatistics.countries');

Route::get('/statistics/available/{year?}', 'StatisticsController@getAvailableDates')
    ->where([
        'year' => '\d{4}'
    ])
    ->name('visitorstatistics.available_dates');
