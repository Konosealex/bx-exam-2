<?php

const ADMIN_GROUP_ID = 1;
const MAIL_EVENT = 'NEW_REGISTRATION';
const MAIL_EVENT_2 = 'EXAM_CHECK_COUNT';

function checkUserCount()
{
    $lastUserId = COption::GetOptionInt("main", "lastUserId", "0");
    $obUsers = CUser::GetList(($by = "id"), ($order = "desc"), ['>ID' => $lastUserId], ['FIELDS' => ['ID']]);
    $newUsersCount = $obUsers->SelectedRowsCount();
    if ($newUsersCount) {
        $arUser = $obUsers->GetNext();
        $newlastUserId = $arUser['ID'];
        $timeUserCheck = COption::GetOptionInt("main", "timeUserCheck", "0");

        if ($timeUserCheck) {
            $days = round((time() - $timeUserCheck) / 86400);
            if (!$days) {
                $days = 1;
            }
        } else {
            $days = 1;
        }

        $arFields = [
            'COUNT_USERS' => $newUsersCount,
            'COUNT_DAYS'  => $days
        ];

        $obUsers = CUser::GetList(
            ($by = "id"),
            ($order = "desc"),
            ['GROUPS_ID' => [ADMIN_GROUP_ID]],
            ['FIELDS' => ['ID', 'EMAIL']]
        );
        while ($arUser = $obUsers->GetNext()) {
            $arFields['EMAIL'] = $arUser['EMAIL'];

            CEvent::Send
            (
                MAIL_EVENT,
                SITE_ID,
                $arFields
            );
        }
        COption::SetOptionInt("main", "lastUserId", $newlastUserId);
    }
    COption::SetOptionInt("main", "timeUserCheck", time());

    return 'checkUserCount();';
}

function examCheckCount()
{
    $by = "id";
    $order = "asc";
    $arFilter = [
        'ID' => 1
    ];
    $arUser = CUser::GetList($by, $order, $arFilter)->GetNext();
    if ($arUser) {
        CEvent::Send(
            MAIL_EVENT_2,
            's1',
            [
                'MAIL_TO' => $arUser['EMAIL'],
                'COUNT'   => CUser::GetCount(),
            ]
        );
    }

    return 'examCheckCount();';
}