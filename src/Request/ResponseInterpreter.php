<?php

namespace Jpietrzyk\VoltDbTools\Request;


interface ResponseInterpreter {

    const STATUS_CONNECTION_LOST = -4;
    const STATUS_CONNECTION_TIMEOUT = -6;
    const STATUS_GRACEFUL_FAILURE = -2;
    const STATUS_OPERATIONAL_FAILURE = -9;
    const STATUS_RESPONSE_UNKNOWN = -7;
    const STATUS_SERVER_UNAVAILABLE = -5;
    const STATUS_SUCCESS = 1;
    const STATUS_TXN_RESTART = -8;
    const STATUS_UNEXPECTED_FAILURE = -3;
    const STATUS_UNINITIALIZED_APP_STATUS_CODE = -128;
    const STATUS_USER_ABORT = -1;


}