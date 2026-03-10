<?php

namespace ShabatShalom;

/**
 * Shabat Shalom PHP Library
 * For calculating Shabbat times
 */
class ShabatShalom {
    private float $latitude;
    private float $longitude;
    private string $timezone;
    private ?float $elevation;

    // City database with coordinates and timezones
    private static array $cities = [
        'Jerusalem' => ['latitude' => 31.7690, 'longitude' => 35.2163, 'timezone' => 'Asia/Jerusalem', 'country' => 'Israel'],
        'Tel Aviv' => ['latitude' => 32.0809, 'longitude' => 34.7806, 'timezone' => 'Asia/Jerusalem', 'country' => 'Israel'],
        'Haifa' => ['latitude' => 32.7940, 'longitude' => 34.9896, 'timezone' => 'Asia/Jerusalem', 'country' => 'Israel'],
        'Beer Sheva' => ['latitude' => 31.2530, 'longitude' => 34.7915, 'timezone' => 'Asia/Jerusalem', 'country' => 'Israel'],
        'Rishon LeZion' => ['latitude' => 31.9730, 'longitude' => 34.7925, 'timezone' => 'Asia/Jerusalem', 'country' => 'Israel'],
        'Petah Tikva' => ['latitude' => 32.0870, 'longitude' => 34.8870, 'timezone' => 'Asia/Jerusalem', 'country' => 'Israel'],
        'Ashdod' => ['latitude' => 31.8024, 'longitude' => 34.6550, 'timezone' => 'Asia/Jerusalem', 'country' => 'Israel'],
        'Netanya' => ['latitude' => 32.3215, 'longitude' => 34.8573, 'timezone' => 'Asia/Jerusalem', 'country' => 'Israel'],
        'Bat Yam' => ['latitude' => 32.0164, 'longitude' => 34.7772, 'timezone' => 'Asia/Jerusalem', 'country' => 'Israel'],
        'Bnei Brak' => ['latitude' => 32.0838, 'longitude' => 34.8340, 'timezone' => 'Asia/Jerusalem', 'country' => 'Israel'],
        'New York' => ['latitude' => 40.7143, 'longitude' => -74.0060, 'timezone' => 'America/New_York', 'country' => 'USA'],
        'London' => ['latitude' => 51.5099, 'longitude' => -0.1181, 'timezone' => 'Europe/London', 'country' => 'UK'],
        'Paris' => ['latitude' => 48.8566, 'longitude' => 2.3522, 'timezone' => 'Europe/Paris', 'country' => 'France'],
        'Los Angeles' => ['latitude' => 34.0522, 'longitude' => -118.2437, 'timezone' => 'America/Los_Angeles', 'country' => 'USA'],
        'Toronto' => ['latitude' => 43.6532, 'longitude' => -79.3832, 'timezone' => 'America/Toronto', 'country' => 'Canada'],
        'Miami' => ['latitude' => 25.7617, 'longitude' => -80.1918, 'timezone' => 'America/New_York', 'country' => 'USA'],
        'Chicago' => ['latitude' => 41.8781, 'longitude' => -87.6298, 'timezone' => 'America/Chicago', 'country' => 'USA'],
        'Buenos Aires' => ['latitude' => -34.6037, 'longitude' => -58.3816, 'timezone' => 'America/Argentina/Buenos_Aires', 'country' => 'Argentina'],
        'Melbourne' => ['latitude' => -37.8136, 'longitude' => 144.9631, 'timezone' => 'Australia/Melbourne', 'country' => 'Australia'],
        'Johannesburg' => ['latitude' => -26.2041, 'longitude' => 28.0473, 'timezone' => 'Africa/Johannesburg', 'country' => 'South Africa']
    ];

    public function __construct(float $latitude = null, float $longitude = null, string $timezone = null, float $elevation = null) {
        // If no parameters provided, use Jerusalem as default
        if ($latitude === null && $longitude === null && $timezone === null) {
            $jerusalem = self::$cities['Jerusalem'];
            $this->latitude = $jerusalem['latitude'];
            $this->longitude = $jerusalem['longitude'];
            $this->timezone = $jerusalem['timezone'];
        } else {
            $this->latitude = $latitude ?? 0.0;
            $this->longitude = $longitude ?? 0.0;
            $this->timezone = $timezone ?? 'UTC';
        }
        $this->elevation = $elevation;
    }

    /**
     * Get Shabbat times for the current week
     */
    public function get_shabbat_times(): array {
        $today = new DateTime();
        $friday = $this->get_next_friday($today);
        $saturday = $this->get_next_saturday($today);

        return [
            'date' => $friday->format('Y-m-d'),
            'candleLighting' => $this->calculate_candle_lighting($friday),
            'havdalah' => $this->calculate_havdalah($saturday)
        ];
    }

    /**
     * Calculate candle lighting time (18 minutes before sunset)
     */
    public function calculate_candle_lighting(DateTime $date): DateTime {
        $sunset = $this->calculate_sunset($date);
        return $this->add_minutes($sunset, -18);
    }

    /**
     * Calculate havdalah time (42 minutes after sunset)
     */
    public function calculate_havdalah(DateTime $date): DateTime {
        $sunset = $this->calculate_sunset($date);
        return $this->add_minutes($sunset, 42);
    }

    /**
     * Simplified sunrise calculation
     */
    public function calculate_sunrise(DateTime $date): DateTime {
        $day_of_year = $this->get_day_of_year($date);
        $declination = $this->calculate_declination($day_of_year);
        $hour_angle = $this->calculate_hour_angle($declination, -0.83);

        $sunrise_time = 12 - $hour_angle / 15;
        return $this->convert_to_time($sunrise_time, $date);
    }

    /**
     * Simplified sunset calculation
     */
    public function calculate_sunset(DateTime $date): DateTime {
        $day_of_year = $this->get_day_of_year($date);
        $declination = $this->calculate_declination($day_of_year);
        $hour_angle = $this->calculate_hour_angle($declination, -0.83);

        $sunset_time = 12 + $hour_angle / 15;
        return $this->convert_to_time($sunset_time, $date);
    }

    /**
     * Get day of year
     */
    private function get_day_of_year(DateTime $date): int {
        return (int)$date->format('z');
    }

    /**
     * Calculate solar declination
     */
    private function calculate_declination(int $day_of_year): float {
        return -23.45 * cos((360 * ($day_of_year + 10) / 365) * pi() / 180);
    }

    /**
     * Calculate hour angle
     */
    private function calculate_hour_angle(float $declination, float $zenith): float {
        $lat_rad = $this->latitude * pi() / 180;
        $dec_rad = $declination * pi() / 180;
        $zen_rad = $zenith * pi() / 180;

        $cos_hour_angle = (cos($zen_rad) - sin($lat_rad) * sin($dec_rad)) /
                       (cos($lat_rad) * cos($dec_rad));

        return acos($cos_hour_angle) * 180 / pi();
    }

    /**
     * Convert decimal time to DateTime
     */
    private function convert_to_time(float $decimal_time, DateTime $date): DateTime {
        $hours = floor($decimal_time);
        $minutes = floor(($decimal_time - $hours) * 60);
        $seconds = floor(((($decimal_time - $hours) * 60) - $minutes) * 60);

        $result = clone $date;
        $result->setTime($hours, $minutes, $seconds);
        return $result;
    }

    /**
     * Add minutes to a DateTime
     */
    private function add_minutes(DateTime $date, int $minutes): DateTime {
        $result = clone $date;
        $result->modify("$minutes minutes");
        return $result;
    }

    /**
     * Get next Friday from given date
     */
    private function get_next_friday(DateTime $date): DateTime {
        $day_of_week = (int)$date->format('w');
        $days_until_friday = ((5 - $day_of_week + 7) % 7) ?: 7;
        $friday = clone $date;
        $friday->modify("+$days_until_friday days");
        return $friday;
    }

    /**
     * Get next Saturday from given date
     */
    private function get_next_saturday(DateTime $date): DateTime {
        $day_of_week = (int)$date->format('w');
        $days_until_saturday = ((6 - $day_of_week + 7) % 7) ?: 7;
        $saturday = clone $date;
        $saturday->modify("+$days_until_saturday days");
        return $saturday;
    }

    /**
     * Format time for display
     */
    public function format_time(DateTime $time): string {
        return $time->format('H:i');
    }

    /**
     * Get city information by name
     */
    public static function get_city_info(string $city_name): array {
        if (!isset(self::$cities[$city_name])) {
            throw new InvalidArgumentException("City '{$city_name}' not found in database");
        }
        return self::$cities[$city_name];
    }

    /**
     * Get all cities in a specific country
     */
    public static function get_cities_in_country(string $country): array {
        $result = [];
        foreach (self::$cities as $name => $info) {
            if ($info['country'] === $country) {
                $result[$name] = $info;
            }
        }
        return $result;
    }

    /**
     * Get all Israeli cities
     */
    public static function get_all_israeli_cities(): array {
        return self::get_cities_in_country('Israel');
    }

    /**
     * Search cities by name pattern
     */
    public static function search_cities(string $query): array {
        $result = [];
        $lower_query = strtolower($query);
        foreach (self::$cities as $name => $info) {
            if (str_contains(strtolower($name), $lower_query)) {
                $result[$name] = $info;
            }
        }
        return $result;
    }

    /**
     * Check if it's currently Shabbat
     */
    public function is_shabat(DateTime $date = null): bool {
        $now = new DateTime();
        if ($date === null) {
            $date = $now;
        }
        
        $friday = $this->get_next_friday($date);
        $saturday = $this->get_next_saturday($date);
        
        $candleLighting = $this->calculate_candle_lighting($friday);
        $havdalah = $this->calculate_havdalah($saturday);
        
        return $now >= $candleLighting && $now <= $havdalah;
    }
}
