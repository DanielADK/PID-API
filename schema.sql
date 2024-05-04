create table if not exists opening_hours
(
    id        int auto_increment
        primary key,
    day_from  int  not null,
    day_to    int  not null,
    time_from time not null,
    time_to   time not null,
    constraint openinghoursIdx
        unique (day_from, day_to, time_from, time_to)
)
    collate = utf8mb4_unicode_ci;

create table if not exists point_of_sale
(
    id          varchar(255) not null
        primary key,
    type        varchar(255) not null,
    name        varchar(255) not null,
    address     varchar(255) null,
    latitude    double       not null,
    longitude   double       not null,
    services    int          not null,
    pay_methods int          not null,
    link        varchar(255) null
)
    collate = utf8mb4_unicode_ci;

create table if not exists point_of_sale_opening_hours
(
    point_of_sale_id varchar(255) not null,
    opening_hours_id int          not null,
    primary key (point_of_sale_id, opening_hours_id),
    constraint FK_E42839DC6B7E9A73
        foreign key (point_of_sale_id) references point_of_sale (id)
            on delete cascade,
    constraint FK_E42839DCCE298D68
        foreign key (opening_hours_id) references opening_hours (id)
            on delete cascade
)
    collate = utf8mb4_unicode_ci;

create index IDX_E42839DC6B7E9A73
    on point_of_sale_opening_hours (point_of_sale_id);

create index IDX_E42839DCCE298D68
    on point_of_sale_opening_hours (opening_hours_id);

