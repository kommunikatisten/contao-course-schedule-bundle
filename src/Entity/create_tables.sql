use contao;

drop view if exists komm_sch_teachers;
drop view if exists komm_sch_courses;
drop view if exists komm_sch_subjects;
drop table if exists komm_sch_teacher_course;
drop table if exists komm_sch_teacher_subject;
drop table if exists komm_sch_teacher;
drop table if exists komm_sch_course;
drop table if exists komm_sch_subject;
drop table if exists komm_sch_room;

create table komm_sch_teacher (
    teacher_id    int          not null auto_increment,
    last_modified datetime     not null default CURRENT_TIMESTAMP,
    name          varchar(255) not null default 'please set a name',
    constraint komm_sch_teacher_pk primary key (teacher_id)
)
    engine InnoDB;

insert into komm_sch_teacher
    (name)
values
    ('Rodrico Valdez'),
    ('Maria Suarrez'),
    ('Tina Stein'),
    ('Steffan Morgenroth')
;

create table komm_sch_subject (
    subject_id    int          not null auto_increment,
    last_modified datetime     not null default CURRENT_TIMESTAMP,
    name          varchar(255) not null default 'please set a name',
    css_class     varchar(255) not null default '',
    description   text         not null,
    constraint komm_sch_subject_pk primary key (subject_id),
    constraint komm_sch_subject_ux unique index (name)
)
    engine InnoDB;

insert into komm_sch_subject
    (name, css_class, description)
values
    ('Tanzkreis', 'tanz_kreis', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.'),
    ('Grundkurs', 'k_grund', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.'),
    ('Level 2', 'k_level_2', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.'),
    ('Level 3', 'k_level_3', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.');

create table komm_sch_room (
    room_id int          not null auto_increment,
    last_modified datetime     not null default CURRENT_TIMESTAMP,
    name    varchar(255) not null,
    constraint komm_sch_room_pk primary key (room_id)
);

insert into komm_sch_room
    (room_id, name)
values
    (1, 'Saal A'),
    (2, 'Saal B');

create table komm_sch_course (
    course_id     int          not null auto_increment,
    last_modified datetime     not null default CURRENT_TIMESTAMP,
    name          varchar(255) not null,
    start_date    date                  default null,
    start_time    time         not null,
    end_date      date                  default null,
    end_time      time         not null,
    day_index     tinyint      not null default 1,
    subject_id    int          not null,
    room_id       int          not null,
    constraint komm_sch_course_pk primary key (course_id),
    constraint komm_sch_course_subject_fk foreign key (subject_id)
        references komm_sch_subject (subject_id)
        on update cascade
        on delete restrict,
    constraint komm_sch_course_room_fk foreign key (room_id)
        references komm_sch_room (room_id)
        on update cascade
        on delete restrict,
    constraint komm_sch_course_ux unique index (name),
    constraint komm_sch_course_room_time_ux unique (day_index, start_time, room_id)
)
    engine InnoDB;



insert into komm_sch_course
    (name, start_time, end_time, day_index, start_date, end_date, subject_id, room_id)
values
    ('Mo 1. TK', '16:15', '17:45', 1, null, null, 1, 1),
    ('Mo 2. TK', '18:00', '19:30', 1, null, null, 1, 1),
    ('Mo 2.a TK', '19:00', '20:30', 1, null, null, 1, 1),
    ('Mo 3. TK', '19:35', '21:05', 1, null, null, 1, 2),
    ('Mi 1. TK', '18:00', '19:25', 3, null, null, 1, 1),
    ('Do 1. TK', '19:45', '21:15', 4, null, null, 1, 1),
    ('Fr 1. TK', '18:00', '19:30', 5, null, null, 1, 1),
    ('Fr 2. TK', '19:55', '21:15', 5, null, null, 1, 1),
    ('So 1. TK', '14:30', '16:00', 7, null, null, 1, 1),
    ('So 2. TK', '15:05', '17:00', 7, null, null, 1, 2),
    ('So 3. TK', '13:00', '15:00', 7, null, null, 1, 2),
    ('Di GK', '14:00', '15:30', 2, null, null, 2, 1),
    ('Lev3 Di', '16:00', '17:30', 2, '2020-11-24', null, 4, 1)
;

create table komm_sch_teacher_subject (
    subject_id int not null,
    teacher_id int not null,
    sorting    int not null default 1,
    constraint komm_sch_teacher_subject_pk primary key (subject_id, teacher_id),
    constraint komm_sch_teacher_subject_subject_fk foreign key (subject_id)
        references komm_sch_subject (subject_id)
        on delete cascade
        on update cascade,
    constraint komm_sch_teacher_subject_teacher_fk foreign key (teacher_id)
        references komm_sch_teacher (teacher_id)
        on delete cascade
        on update cascade,
    constraint komm_sch_teacher_subject_ux unique index (subject_id, teacher_id, sorting)
)
    engine InnoDB;

insert into komm_sch_teacher_subject
    (subject_id, teacher_id, sorting)
values
    (1, 1, 1),
    (1, 2, 2),
    (2, 1, 1),
    (2, 2, 2),
    (3, 3, 1),
    (3, 4, 2),
    (4, 3, 2),
    (4, 4, 1);



create table komm_sch_teacher_course (
    teacher_id int not null,
    course_id  int not null,
    constraint komm_sch_teacher_course_pk primary key (teacher_id, course_id),
    constraint komm_sch_teacher_course_teacher_fk foreign key (teacher_id)
        references komm_sch_teacher (teacher_id)
        on update cascade
        on delete cascade,
    constraint komm_sch_teacher_course__course_fk foreign key (course_id)
        references komm_sch_course (course_id)
        on update cascade
        on delete cascade
)
    engine InnoDB;


insert into komm_sch_teacher_course
    (teacher_id, course_id)
select
    teacher_id,
    course_id
from
    komm_sch_teacher_subject       t
        inner join komm_sch_course c
                   on c.subject_id = t.subject_id
;

create view komm_sch_teachers as
    select
        t.teacher_id    teacher_id,
        t.name          teacher_name,
        t.last_modified teacher_last_modified,
        s.subject_id    subject_id,
        s.name          subject_name,
        s.description   subject_description,
        s.css_class     subject_css_class,
        s.last_modified subject_last_modified,
        c.course_id     course_id,
        c.name          course_name,
        c.start_date    course_start_date,
        c.start_time    course_start_time,
        c.end_time      course_end_time,
        c.last_modified course_last_modified
    from
        komm_sch_teacher                       t
            left join komm_sch_teacher_subject ksts
                      on t.teacher_id = ksts.teacher_id
            left join komm_sch_subject         s
                      on s.subject_id = ksts.subject_id
            left join komm_sch_teacher_course  kstc
                      on t.teacher_id = kstc.teacher_id
            left join komm_sch_course          c
                      on kstc.course_id = c.course_id
;

create view komm_sch_courses as
    select
        c.course_id     course_id,
        c.name          course_name,
        c.day_index     course_day_index,
        c.start_date    course_start_date,
        c.start_time    course_start_time,
        c.end_date      course_end_date,
        c.end_time      course_end_time,
        c.last_modified course_last_modified,
        r.room_id       room_id,
        r.name          room_name,
        r.last_modified room_last_modified,
        s.subject_id    subject_id,
        s.name          subject_name,
        s.description   subject_description,
        s.css_class     subject_css_class,
        s.last_modified subject_last_modified,
        t.teacher_id    teacher_id,
        t.name          teacher_name,
        t.last_modified teacher_last_modified
    from
        komm_sch_course                        c
            inner join komm_sch_subject        s
                       using (subject_id)
            inner join komm_sch_room           r
                       using (room_id)
            left join  komm_sch_teacher_course kstc
                       on c.course_id = kstc.course_id
            left join  komm_sch_teacher        t
                       on t.teacher_id = kstc.teacher_id;

create view komm_sch_subjects as
    select
        s.subject_id    subject_id,
        s.name          subject_name,
        s.description   subject_description,
        s.css_class     subject_css_class,
        s.last_modified subject_last_modified,
        c.course_id     course_id,
        c.name          course_name,
        c.day_index     course_day_index,
        c.start_date    course_start_date,
        c.start_time    course_start_time,
        c.end_date      course_end_date,
        c.end_time      course_end_time,
        c.last_modified course_last_modified,
        t.teacher_id    teacher_id,
        t.name          teacher_name,
        t.last_modified teacher_last_modified
    from
        komm_sch_subject                       s
            left join komm_sch_course          c
                      on s.subject_id = c.subject_id
            left join komm_sch_teacher_subject ksts
                      on s.subject_id = ksts.subject_id
            left join komm_sch_teacher         t
                      on ksts.teacher_id = t.teacher_id
