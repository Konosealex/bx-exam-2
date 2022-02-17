<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/kint.phar';
require_once 'include/functions.php';
require_once 'include/handlers.php';
require_once 'include/agents.php';

const PRODUCTION_IBLOCK_ID = 2;
const EVENT_NAME = 'FEEDBACK_FORM';
const CONTENT_MANAGER_GROUP_ID = 5;
const IB_SERVICES = 3;
const IB_COMPLAINT = 8;