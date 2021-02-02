<?php


namespace Kommunikatisten\ContaoScheduleBundle\Service;


use DateInterval;
use DateTime;
use Exception;
use Kommunikatisten\ContaoScheduleBundle\Entity\Course;
use Kommunikatisten\ContaoScheduleBundle\Error\InitializationException;
use Kommunikatisten\ContaoScheduleBundle\Model\Time;
use Kommunikatisten\ContaoScheduleBundle\Repository\CourseRepository;

class CourseScheduleService {

    private CourseRepository $repository;
    private static array $days = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'];

    /**
     * CourseScheduleService constructor.
     * @param CourseRepository $repository
     * @throws InitializationException
     */
    public function __construct(CourseRepository $repository) {
        $this->repository = $repository;
        if ($this->repository == null) {
            throw new InitializationException("Fehler bei der Initialisierung: Repository nicht erreichbar");
        }
    }


    /**
     * @param string|null $dateString
     * @return String[]
     * @throws Exception
     */
    public function getSchedule(string $dateString = null): array {

        $dateString = $dateString == null ? date('Y-m-d') : $dateString;
        $date = new DateTime($dateString);
        $validCourses = $this->repository->findAllValid($dateString);
        $rooms = array();
        $courses = array();
        for ($startTime = 0; $startTime <= 1420; $startTime += 30) {
            $startingCourses = array_filter($validCourses, function(Course $course) use($startTime) {
                $courseStartTime = Time::fromString($course->getStartTime())->format(Time::MINUTES);
                $diff = $courseStartTime - $startTime;
                return abs($diff) < 15 || $diff == -15;
            });
            foreach (self::$days as $dayIndex => $dayName) {
                $dayCourses = array_filter($startingCourses, function (Course $course) use ($dayIndex): bool {
                    return $course->getDayIndex() == $dayIndex + 1;
                });
                if(!empty($dayCourses)) {
                    usort($dayCourses, function (Course $aCourse, Course $bCourse): int {
                        return self::timeDiff($bCourse->getStartTime(), $aCourse->getStartTime());
                    });
                    $courses["$startTime"][$dayName] = array_map(function (Course $course) use ($date, &$rooms, $startTime) {
                        $isFuture = $course->getStartDate() != null && $date->diff(new DateTime($course->getStartDate()))->days > 7;
                        if (!in_array($course->getRoom()->getName(), $rooms, true)) {
                            $rooms[] = $course->getRoom()->getName();
                        }
                        $courseStartTimeMinutes = Time::fromString($course->getStartTime())->format(Time::MINUTES);
                        return array(
                            'dayIndex' => $course->getDayIndex(),
                            'courseStartTimeMinutes' => $courseStartTimeMinutes,
                            'diff' => $courseStartTimeMinutes - $startTime,
                            'subject' => $course->getSubject()->getName(),
                            'room' => $course->getRoom()->getName(),
                            'css' => $course->getSubject()->getCssClass(),
                            'durationMinutes' => self::timeDiff($course->getStartTime(), $course->getEndTime()),
                            'startTime' => $course->getStartTime(),
                            'endTime' => $course->getEndTime(),
                            'future' => $isFuture,
                            'startDate' => $course->getStartDate());
                    }, $dayCourses);
                }
            }
        }
        $schedule['courses'] = $courses;
        $schedule['rooms'] = $rooms;
        $schedule['days'] = self::$days;
        $schedule['previous'] = $date->sub(new DateInterval('P7D'))->format('Y-m-d');
        $schedule['next'] = $date->add(new DateInterval('P7D'))->format('Y-m-d');
        $schedule['currentWeek'] = $date->format('W');
        return $schedule;
    }

    /**
     * @param string $startString
     * @param string $endString
     * @return int
     */
    private static function timeDiff(string $startString, string $endString): int {
        $start = Time::fromString($startString)->format(Time::MINUTES);
        $end = Time::fromString($endString)->format(Time::MINUTES);
        return $end - $start;
    }
}
