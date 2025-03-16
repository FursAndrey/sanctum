<?php

namespace App\Actions\TimeCalculator;

class generateDayEventsAction
{
    public function __invoke(array $graph): array
    {
        $begin = $graph['start'];
        $finish = $graph['end'];
        $result = [];

        /** первый вход может быть до начала дня, или с опозданием */
        $begin = $begin->subSeconds(60 * 60);

        do {
            $events = (new generateEnterExitAction)($begin);

            $tmp = [];
            foreach ($events as $type => $time) {
                $tmp['type'] = $type;
                $tmp['time'] = $time;

                $result[] = $tmp;
            }

            $begin = end($result)['time'];
        } while ($begin->format('H:i') <= $finish->format('H:i'));

        return $result;
    }
}
