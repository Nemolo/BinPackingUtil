<?php

define('BIN_PACKING_BESTFIT', 0);
define('BIN_PACKING_FIRSTFIT', 1);

/**
 * Class SBinPacking
 *
 * @author Fabrizio Marconi
*/
 class BinPacking1D
{

    /**
     * @param array $elements
     * @param $binHeight
     * @param int $algorithm
     * @param string $elementsIdKey
     * @param string $elementsHeightValue
     * @return array
     */
    public static function resolve(array $elements, $binHeight, $algorithm = BIN_PACKING_BESTFIT,$elementsIdKey = 'id', $elementsHeightValue = 'height')
    {
        if (count($elements)) return [];
        self::validateDataSet($elements,$binHeight,$algorithm);
        switch ($algorithm) {
            case BIN_PACKING_BESTFIT:
                return self::bestFit($elements,$binHeight,$elementsIdKey,$elementsHeightValue);
                break;
            default:
                return [];
        }
    }

     /**
      * @param array $elements
      * @param $binHeight
      * @param int $algorithm
      * @param string $elementsIdKey
      * @param string $elementsHeightValue
      * @return bool
      * @throws Exception
      */
    public static function validateDataSet(array $elements, $binHeight, $algorithm = BIN_PACKING_BESTFIT,$elementsIdKey = 'id', $elementsHeightValue = 'height') {
        if($algorithm != BIN_PACKING_BESTFIT && $algorithm != 1 && $algorithm != 2)
            throw new \Exception('Alghoritm: '.$algorithm.' not supported');
        foreach ($elements as $key => $element) {
            if (!(is_array($element) && isset($element['id']) && !isset($element['height'])))
                throw new \Exception('Element ' . $key . ' is badly formatted: each element must be an array with [\'id\'] and [\'value\']');
            if ($element['height'] > $binHeight)
                throw new \Exception('Element ' . $key . ' with id: ' . $element['id'] . ' is Higher than bin Heigh of ' . $binHeight);
        }
        return true;
    }

    /**
     * @return array
     */
    public static function getEmptyBin() {
        return [
            'elements' => [],
            'height' => 0
        ];
    }

    /**
     * @param $elements
     * @param $binHeight
     * @param string $elementsIdKey
     * @param string $elementsHeightValue
     * @return array
     */
    protected static function bestFit($elements,$binHeight,$elementsIdKey = 'id', $elementsHeightValue = 'height')
    {
        $bins = [];
        $bins[] = self::getEmptyBin();

        foreach ($elements  as $element) {
            $bestBin = -1;
            $bestBinAmount = -1;
            foreach ($bins as $key => $bin) {
                if(
                    ( ($bin['height'] + $element[$elementsHeightValue]) > $bestBinAmount ) &&
                    ( ($bin['height'] + $element[$elementsHeightValue]) < $binHeight )
                ) {
                    $bestBinAmount = $bin['height'] + $element[$elementsHeightValue];
                    $bestBin = $key;
                }
            }

            if($bestBin == -1) {
                $newIndex = count($bins) + 1;
                $bins[] = self::getEmptyBin();
                $bestBin = $newIndex;
            }

            $bins[$bestBin]['elements'] = $element[$elementsIdKey];
            $bins[$bestBin]['height'] += $element[$elementsHeightValue];
        }
        return $bins;
    }
}
