export interface Statistics {
    month: number;
    month_name: string;
    ls_range: string;
    season: string;
    season_description: string;
    statistics: {
        average_temp: number;
        absolute_min: number;
        absolute_max: number;
        total_sols: number;
    };
    date_range: {
        first_date: string;
        last_date: string;
    };
    ls_actual: {
        min: number;
        max: number;
    };
}

export interface GeneratedImage {
    image: string;
    interpretation: string;
    month: number;
    weather_context: Statistics;
}

export interface MarsImageRecord {
    id: number;
    mars_month: number;
    image_url: string;
    interpretation: string;
    weather_context: Statistics;
    created_at: string;
}
