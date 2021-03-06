<?php

namespace Rizza\CalendarBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Rizza\CalendarBundle\DateProcessor\DayOfTheMonth;
use Rizza\CalendarBundle\DateProcessor\DayOfTheYear;

abstract class Recurrence implements RecurrenceInterface
{
    const DAY_SUNDAY = 0;
    const DAY_MONDAY = 1;
    const DAY_TUESDAY = 2;
    const DAY_WEDNESDAY = 3;
    const DAY_THURSDAY = 4;
    const DAY_FRIDAY = 5;
    const DAY_SATURDAY = 6;

    const FREQUENCY_DAILY = 0;
    const FREQUENCY_WEEKLY = 1;
    const FREQUENCY_MONTHLY = 2;
    const FREQUENCY_YEARLY = 3;
    
    protected $id;

    protected $event;

    /**
     * An array of strings representing the days of the week on which this
     * recurrence occurs. Possible integer values are sunday, monday, tuesday,
     * wednesday, thursday, friday and saturday. If you set $days, you must also
     * set $dayFrequency. For example, if $days is 1 and $dayFrequency is 0,
     * then the recurrence is every Monday.
     *
     * @return array
     */
    protected $days;

    /**
     * An array of integers used in combination with $days to specify which week
     * within a month or year this recurrence occurs. For example, if $frequency
     * is monthly, $days is 0 and $dayFrequency contains 2, then the recurrence
     * will occur the second Monday of every month.
     *
     * @var array
     */
    protected $dayFrequency;

    /**
     * An array of numbers, with integer values ranging from 1 to 12, that
     * indicate the months within a year that this recurrence occurs.
     *
     * @var array
     */
    protected $months;

    /**
     * An array of numbers, with integer values ranging from 1 to 31 or -31 to
     * -1, that indicate the days within a month that this recurrence occurs.
     * Negative values indicate the number of days from the last day of the month.
     *
     * @var array
     */
    protected $monthDays;

    /**
     * An array of numbers, with integer values ranging from 1 to 53 or -53 to -1,
     * that indicate the weeks within a year that this recurrence occurs.
     * Negative values indicate the number of weeks from the last week of the year.
     *
     * @var array
     */
    protected $weekNumbers;

    /**
     * An array of numbers, with integer values ranging from 1 to 366 or -366
     * to -1, that indicate the days within a year that this recurrence occurs.
     * Negative values indicate the number of days from the last day of the year.
     *
     * @var array
     */
    protected $yearDays;

    /**
     * The frequency of this recurrence specified by a constant. Possible values
     * are 0 (daily), 1 (weekly), 2 (monthly), or 3 (yearly).
     *
     * @var string
     */
    protected $frequency;

    /**
     * A positive integer indicating how often the specified frequency repeats.
     * For example, if $frequency is daily, then an interval value of 2
     * indicates a recurrence every two days.
     *
     * @var int
     */
    protected $interval;

    /**
     * The end date of this recurrence.
     *
     * @var DateTime
     */
    protected $until;

    /**
     * A string that indicates the start day of the week. Possible values are
     * 0 (sunday) - 6 (saturday).
     * 
     * @var int
     */
    protected $weekStartDay;

    public function getId()
    {
        return $this->id;
    }

    public function setEvent(EventInterface $event)
    {
        $this->event = $event;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getDays()
    {
        return $this->days ?: $this->days = new ArrayCollection();
    }

    public function addDay($day)
    {
        $day = intval($day);
        if (!$this->getDays()->contains($day)) {
            $this->getDays()->add($day);
        }
    }

    public function removeDay($day)
    {
        $day = intval($day);
        if ($this->getDays()->contains($day)) {
            $this->getDays()->removeElement($day);
        }
    }

    public function getDayFrequency()
    {
        return $this->dayFrequency ?: $this->dayFrequency = new ArrayCollection();
    }

    public function addDayFrequency($frequency)
    {
        $frequency = intval($frequency);
        if (6 < $frequency || 0 > $frequency) {
            throw new \RangeException('Day frequency cannot be less than 0 or greater than 6');
        }

        if (!$this->getDayFrequency()->contains($frequency)) {
            $this->getDayFrequency()->add($frequency);
        }
    }

    public function removeDayFrequency($frequency)
    {
        if ($this->getDayFrequency()->contains($frequency)) {
            $this->getDayFrequency()->removeElement($frequency);
        }
    }

    public function getMonths()
    {
        return $this->months ?: $this->months = new ArrayCollection();
    }

    public function addMonth($month)
    {
        $month = intval($month);
        if (!$this->getMonths()->contains($month)) {
            $this->getMonths()->add($month);
        }
    }

    public function removeMonth($month)
    {
        $month = intval($month);
        if ($this->getMonths()->contains($month)) {
            $this->getMonths()->removeElement($month);
        }
    }

    public function getMonthDays()
    {
        return $this->monthDays ?: $this->monthDays = new ArrayCollection();
    }

    public function addMonthDay($day)
    {
        $day = intval($day);

        if (31 < $day || -31 > $day || 0 == $day) {
            throw new \RangeException('Month day must be between -1 to -31 or 1 to 31');
        }

        if (!$this->getMonthDays()->contains($day)) {
            $this->getMonthDays()->add($day);
        }
    }

    public function removeMonthDay($day)
    {
        $day = intval($day);
        if ($this->getMonthDays()->contains($day)) {
            $this->getMonthDays()->removeElement($day);
        }
    }

    public function getWeekNumbers()
    {
        return $this->weekNumbers ?: $this->weekNumbers = new ArrayCollection();
    }

    public function addWeekNumber($week)
    {
        $week = intval($week);
        if (!$this->getWeekNumbers()->contains($week)) {
            $this->getWeekNumbers()->add($week);
        }
    }

    public function removeWeekNumber($week)
    {
        $week = intval($week);
        if ($this->getWeekNumbers()->contains($week)) {
            $this->getWeekNumbers()->removeElement($week);
        }
    }

    public function getYearDays()
    {
        return $this->yearDays ?: $this->yearDays = new ArrayCollection();
    }

    public function addYearDay($day)
    {
        $day = intval($day);
        if (!$this->getYearDays()->contains($day)) {
            $this->getYearDays()->add($day);
        }
    }

    public function removeYearDay($day)
    {
        $day = intval($day);
        if ($this->getYearDays()->contains($day)) {
            $this->getYearDays()->removeElement($day);
        }
    }

    public function setFrequency($frequency)
    {
        $validFrequencies = array(self::FREQUENCY_DAILY, self::FREQUENCY_MONTHLY,
                                  self::FREQUENCY_WEEKLY, self::FREQUENCY_YEARLY);
        if (!in_array($frequency, $validFrequencies)) {
            throw new \InvalidArgumentException('Invalid frequency value provided');
        }
        
        $this->frequency = $frequency;
    }
    
    public function getFrequency()
    {
        return $this->frequency;
    }

    public function setInterval($interval)
    {
        $this->interval = abs(intval($interval));
    }

    public function getInterval()
    {
        return $this->interval;
    }
    
    public function setUntil(\DateTime $until)
    {
        $this->until = $until;
    }
    
    public function getUntil()
    {
        return $this->until;
    }

    public function setWeekStartDay($day)
    {
        $validDays = array(self::DAY_SUNDAY, self::DAY_MONDAY, self::DAY_TUESDAY,
                           self::DAY_WEDNESDAY, self::DAY_THURSDAY,
                           self::DAY_FRIDAY, self::DAY_SATURDAY);
        if (!in_array($day, $validDays)) {
            throw new \InvalidArgumentException('Invalid week start day provided');
        }

        $this->weekStartDay = $day;
    }
    
    public function getWeekStartDay()
    {
        return $this->weekStartDay;
    }

    public function contains(\DateTime $dateTime)
    {
        $onDate = true;

        if ($this->until instanceof \DateTime && $dateTime->format('Y-m-d') > $this->until->format('Y-m-d')) {
            $onDate = false;
        }

        if ($this->getMonths()->count() && !$this->getMonths()->contains((int) $dateTime->format('n'))) {
            $onDate = false;
        }

        if ($this->getWeekNumbers()->count() && !$this->getWeekNumbers()->contains((int) $dateTime->format('W'))) {
            $onDate = false;
        }

        if ($this->getDays()->count() && !$this->getDays()->contains((int) $dateTime->format('j'))) {
            $onDate = false;
        }

        if (!$this->onYearDays($dateTime) || !$this->onMonthDays($dateTime)) {
            $onDate = false;
        }

        return $onDate;
    }

    protected function onDayFrequency(\DateTime $dateTime)
    {
        if ($this->frequency == self::FREQUENCY_DAILY || !$this->dayFrequency->count() || !$this->days->count()) return true;

        // This needs $interval integrated as well... yay.
        while ($this->days->next()) {
            $day = $this->days->current();
            while ($this->dayFrequency->next()) {

                switch($this->frequency) {
                    case self::FREQUENCY_WEEKLY:
                        $compare = jddayofweek(cal_to_jd(CAL_GREGORIAN, $dateTime->format('m'), $dateTime->format('j'), $dateTime->format('Y')));
                        break;

                    case self::FREQUENCY_MONTHLY:
                        throw new \Exception('Not implemented');
                        break;

                    case self::FREQUENCY_YEARLY:
                        $compare = $dateTime->format('z');
                        break;

                    default:
                        throw new \UnexpectedValueException("The provided frequency `{$this->frequency}` is invalid");
                }

                if ($compare === $day) return true;
            }
        }

        return false;
    }

    protected function onMonthDays(\DateTime $dateTime)
    {
        if (!$this->getMonthDays()->count()) return true;

        $dotm = new DayOfTheMonth();
        while ($this->monthDays->next()) {
            if ($dotm->setDay($this->monthDays->current())->contains($dateTime))
                return true;
        }
        return false;
    }

    protected function onYearDays(\DateTime $dateTime)
    {
        if (!$this->getYearDays()->count()) return true;

        $doty = new DayOfTheYear();
        while ($this->yearDays->next()) {
            if ($doty->setDay($this->yearDays->current())->contains($dateTime))
                return true;
        }

        return false;
    }

    public function getOccurrences(\DateTime $betweenStart = null, \DateTime $betweenEnd = null)
    {
        if (!$betweenEnd) {
            if (!$this->until) {
                throw new \InvalidArgumentException('Cannot get occurrences on an infinite recurrence without using an end constraint.');
            }
            $betweenEnd = $this->until;
        }

        $endDate = $betweenEnd->format('Y-m-d');

        $occurrences = new ArrayCollection();
        while (($date = $betweenStart->format('Y-m-d')) < $endDate) {
            if ($this->contains($betweenStart))
                $this->occurrences->add($date);

            $betweenStart->add('+1 days');
        }

        return $occurrences;
    }
}