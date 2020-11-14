use contao;
create table komm_schb_teacher (
    teacher_id int not null auto_increment,
    last_modified datetime not null default CURRENT_TIMESTAMP,
    name varchar(255) not null default 'please set a name',
    constraint komm_schb_teacher_pk primary key (teacher_id)
) engine MyISAM;

insert into komm_schb_teacher (name) values
    ('Rodrico Valdez'),
    ('Maria Suarrez'),
    ('Tina Stein'),
    ('Steffan Morgenroth')
;
