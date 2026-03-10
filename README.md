# Details

### Author: Edan
### Version: 1.0.0

A PHP library for calculating Shabbat times with accurate astronomical calculations

---

# Documentation

## Steps

### 1. Installation

> `composer require edanick/shabat-shalom`

### 2. Import

> Import ShabatShalom class

```php
require_once 'src/ShabatShalom.php';

use ShabatShalom\ShabatShalom;
```

---

# Samples

> Sample of Shabbat time calculation for Jerusalem

Jerusalem coordinates (31.7683° N, 35.2137° E) with timezone 'Asia/Jerusalem' will calculate candle lighting 18 minutes before sunset and havdalah 42 minutes after sunset for the upcoming Shabbat

> The library supports all major Israeli cities and international cities with built-in city database

---

# Functions

## Introduction

> There are 7 main functions in total

1. get_shabbat_times
2. is_shabat
3. calculate_candle_lighting
4. calculate_havdalah
5. calculate_sunrise
6. calculate_sunset
7. format_time

> Plus 4 static city database functions

1. get_city_info
2. get_cities_in_country
3. get_all_israeli_cities
4. search_cities

---

## get_shabbat_times

> Returns Shabbat times for the current or specified week

```php
shabatShalom->get_shabbat_times()
```

---

#### Example

> Gets Shabbat times for Jerusalem (default)

```php
require_once 'src/ShabatShalom.php';

use ShabatShalom\ShabatShalom;

// Default - Jerusalem (no parameters needed)
$shabat_shalom = new ShabatShalom();

// Get Shabbat times for this week in Jerusalem
$shabbat_times = $shabat_shalom->get_shabbat_times();
print_r($shabbat_times);
```

**Result**:
```php
Array(
    [date] => 2024-09-13
    [candle_lighting] => DateTime Object(
        [date] => 2024-09-13 18:23:00.000000
        [timezone_type] => 3
        [timezone] => Asia/Jerusalem
    )
    [havdalah] => DateTime Object(
        [date] => 2024-09-14 19:45:00.000000
        [timezone_type] => 3
        [timezone] => Asia/Jerusalem
    )
)
```

---

## is_shabat

> Returns boolean indicating if it's currently Shabbat at the given location

### Parameters

`date`: **DateTime** (optional)

```php
$shabat_shalom->is_shabat()
```

---

#### Example

> Checks if it's currently Shabbat in Jerusalem

```php
require_once 'src/ShabatShalom.php';

use ShabatShalom\ShabatShalom;

$shabat_shalom = new ShabatShalom();
$currently_shabat = $shabat_shalom->is_shabat();

var_dump($currently_shabat);
```

**Result**:
`bool(true)` or `bool(false)`

---

## get_city_info

> Returns city information including coordinates and timezone

### Parameters

`city_name`: **string**

```php
ShabatShalom::get_city_info(city_name)
```

---

#### Example

> Gets information for Tel Aviv

```php
require_once 'src/ShabatShalom.php';

use ShabatShalom\ShabatShalom;

$tel_aviv_info = ShabatShalom::get_city_info('Tel Aviv');
print_r($tel_aviv_info);
```

**Result**:
```php
Array(
    [latitude] => 32.0853
    [longitude] => 34.7818
    [timezone] => Asia/Jerusalem
    [country] => Israel
)
```

---

## get_all_israeli_cities

> Returns all Israeli cities in the database

```php
ShabatShalom::get_all_israeli_cities()
```

---

#### Example

> Lists all Israeli cities

```php
require_once 'src/ShabatShalom.php';

use ShabatShalom\ShabatShalom;

$israeli_cities = ShabatShalom::get_all_israeli_cities();
print_r(array_keys($israeli_cities));
```

**Result**:
```php
Array(
    [0] => Jerusalem
    [1] => Tel Aviv
    [2] => Haifa
    [3] => Beer Sheva
    [4] => Rishon LeZion
    [5] => Petah Tikva
    [6] => Ashdod
    [7] => Netanya
    [8] => Bat Yam
    [9] => Bnei Brak
)
```

---

## Constructor with Specific Cities

> Create instances for specific cities using coordinates

### Parameters

`latitude`: **float** (optional)\
`longitude`: **float** (optional)\
`timezone`: **string** (optional)\
`elevation`: **float** (optional)

```php
new ShabatShalom(latitude, longitude, timezone, elevation)
```

---

#### Example #1

> Creates instance for Jerusalem

```php
require_once 'src/ShabatShalom.php';

use ShabatShalom\ShabatShalom;

$jerusalem = new ShabatShalom(31.7690, 35.2163, 'Asia/Jerusalem');
$jerusalem_times = $jerusalem->get_shabbat_times();
print_r($jerusalem_times);
```

---

#### Example #2

> Creates instance for New York

```php
require_once 'src/ShabatShalom.php';

use ShabatShalom\ShabatShalom;

$new_york = new ShabatShalom(40.7143, -74.0060, 'America/New_York');
$new_york_times = $new_york->get_shabbat_times();
print_r($new_york_times);
```

---

## format_time

> Formats time to HH:MM format

### Parameters

`time`: **DateTime**

```php
$shabat_shalom->format_time(time)
```

---

#### Example

> Formats candle lighting time

```php
require_once 'src/ShabatShalom.php';

use ShabatShalom\ShabatShalom;

$shabat_shalom = new ShabatShalom();
$times = $shabat_shalom->get_shabbat_times();
$formatted_time = $shabat_shalom->format_time($times['candle_lighting']);

echo $formatted_time;
```

**Result**:
`18:23`

---

## License

This project is licensed under the GNU General Public License v3.0. See the LICENSE file for details.
