<?php

use App\Enum\Status;


function sendStatus($id=Status::Pending)
{
    if ($id != null) {
        $status = [
            Status::Pending   => [
                'name'  => 'Pending',
                'color' => '#ffbd4a'
            ],
            Status::Scheduled => [
                'name'  => 'Scheduled',
                'color' => '#81c868'
            ],
            Status::Sending   => [
                'name'  => 'Sending',
                'color' => '#207a20'
            ],
            Status::Complete  => [
                'name'  => 'Complete',
                'color' => '#b63d7a'
            ],
            Status::Canceled  => [
                'name'  => 'Canceled',
                'color' => '#FF0000'
            ]
        ];

        return $status[$id];
    }
    return null;
}


function getDateTimeNow()
{
    date_default_timezone_set('Africa/Cairo');
    return dateForSearch('');
}

function addMinutesToDateTime($datetime, $minutes)
{
    $time = new DateTime($datetime);
    $time->add(new DateInterval('PT' . $minutes . 'M'));

    return $time->format('Y-m-d H:i:s');
}

function dateForSearch($date, $format = 'Y-m-d H:i:s')
{
    return date_format(date_create($date), $format);
}

function getDayFromDate($date)
{
    $date = DateTime::createFromFormat("Y-m-d", $date);
    return $date->format("d M");
}

function convertStringDateToTime($stringDate, $format = 'Y-m-d H:i:s')
{
    $time = strtotime($stringDate);
    $formatDate = date($format, $time);
    return $formatDate;
}


