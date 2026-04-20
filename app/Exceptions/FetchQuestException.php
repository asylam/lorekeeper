<?php

namespace App\Exceptions;

use Exception;

class FetchQuestException extends Exception {
    public static function invalidQuest() {
        return new self('Invalid quest selected.');
    }

    public static function alreadyAccepted() {
        return new self('Quest already accepted.');
    }

    public static function notAccepted() {
        return new self('Quest not accepted.');
    }

    public static function alreadyCompletedOrExpired() {
        return new self('Quest already completed or expired.');
    }

    public static function requestItemsNotFound() {
        return new self('Quest request items not found.');
    }

    public static function rewardItemsNotFound() {
        return new self('Quest reward items not found.');
    }

    public static function requestedItemMissing() {
        return new self('The requested item no longer exists.');
    }

    public static function insufficientRequestedItems() {
        return new self('You do not have the required item(s) to complete this quest.');
    }

    public static function requestedItemDebitFailed() {
        return new self('An error occurred while removing the item from your inventory. Please try again.');
    }

    public static function requestedCurrencyMissing() {
        return new self('The requested currency no longer exists.');
    }

    public static function insufficientRequestedCurrency() {
        return new self('You do not have the required currency to complete this quest.');
    }

    public static function requestedCurrencyDebitFailed() {
        return new self('An error occurred while removing the currency from your inventory. Please try again.');
    }

    public static function unsupportedRequestType() {
        return new self('Unsupported quest request type encountered.');
    }

    public static function rewardItemMissing() {
        return new self('The reward item no longer exists.');
    }

    public static function rewardCurrencyMissing() {
        return new self('The reward currency no longer exists.');
    }

    public static function unsupportedRewardType() {
        return new self('Unsupported quest reward type encountered.');
    }

    public static function rewardDistributionFailed() {
        return new self('An error occurred while distributing rewards. Please try again.');
    }
}
