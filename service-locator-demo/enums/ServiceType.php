<?php

/**
 * defines the available notification service.
  */
enum ServiceType: string
{
    /** Represents the email notification service. */
    case EMAIL = 'email';

    /** Represents the SMS notification service. */
    case SMS = 'sms';

    /** Represents the disabled (null) notification service. */
    case DISABLE = 'disable';
}