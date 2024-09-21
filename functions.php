<?php
function filterCountries($searchTerm, $countries) {
    $searchTerm = strtolower($searchTerm);
    return array_filter($countries, function($country) use ($searchTerm) {
        return strpos(strtolower($country), $searchTerm) !== false;
    });
}