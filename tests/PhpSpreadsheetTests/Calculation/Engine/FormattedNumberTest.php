<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Engine;

use PhpOffice\PhpSpreadsheet\Calculation\Engine\FormattedNumber;
use PHPUnit\Framework\TestCase;

class FormattedNumberTest extends TestCase
{
    /**
     * @dataProvider providerNumbers
     *
     * @param mixed $expected
     */
    public function testNumber($expected, string $value): void
    {
        FormattedNumber::convertToNumberIfFormatted($value);
        self::assertSame($expected, $value);
    }

    public function providerNumbers(): array
    {
        return [
            [-12.5, '-12.5'],
            [-125.0, '-1.25e2'],
            [0.125, '12.5%'],
        ];
    }

    /**
     * @dataProvider providerFractions
     */
    public function testFraction(string $expected, string $value): void
    {
        $originalValue = $value;
        $result = FormattedNumber::convertToNumberIfFraction($value);
        if ($result === false) {
            self::assertSame($expected, $originalValue);
            self::assertSame($expected, $value);
        } else {
            self::assertSame($expected, (string) $value);
            self::assertNotEquals($value, $originalValue);
        }
    }

    public function providerFractions(): array
    {
        return [
            'non-fraction' => ['1', '1'],
            'common fraction' => ['1.5', '1 1/2'],
            'fraction between -1 and 0' => ['-0.5', '-1/2'],
            'fraction between -1 and 0 with space' => ['-0.5', ' - 1/2'],
            'fraction between 0 and 1' => ['0.75', '3/4 '],
            'fraction between 0 and 1 with space' => ['0.75', ' 3/4'],
            'improper fraction' => ['1.75', '7/4'],
        ];
    }

    /**
     * @dataProvider providerPercentages
     */
    public function testPercentage(string $expected, string $value): void
    {
        $originalValue = $value;
        $result = FormattedNumber::convertToNumberIfPercent($value);
        if ($result === false) {
            self::assertSame($expected, $originalValue);
            self::assertSame($expected, $value);
        } else {
            self::assertSame($expected, (string) $value);
            self::assertNotEquals($value, $originalValue);
        }
    }

    public function providerPercentages(): array
    {
        return [
            'non-percentage' => ['10', '10'],
            'single digit percentage' => ['0.02', '2%'],
            'two digit percentage' => ['0.13', '13%'],
            'negative single digit percentage' => ['-0.07', '-7%'],
            'negative two digit percentage' => ['-0.75', '-75%'],
            'large percentage' => ['98.45', '9845%'],
            'small percentage' => ['0.0005', '0.05%'],
            'percentage with decimals' => ['0.025', '2.5%'],
            'trailing percent with space' => ['0.02', '2 %'],
            'trailing percent with leading and trailing space' => ['0.02', ' 2 % '],
            'leading percent with decimals' => ['0.025', ' % 2.5'],

            //These should all fail
            'percent only' => ['%', '%'],
            'nonsense percent' => ['2%2', '2%2'],
            'negative leading percent' => ['-0.02', '-%2'],

            //Percent position permutations
            'permutation_1' => ['0.02', '2%'],
            'permutation_2' => ['0.02', ' 2%'],
            'permutation_3' => ['0.02', '2% '],
            'permutation_4' => ['0.02', ' 2 % '],
            'permutation_5' => ['0.0275', '2.75% '],
            'permutation_6' => ['0.0275', ' 2.75% '],
            'permutation_7' => ['0.0275', ' 2.75 % '],
            'permutation_8' => [' 2 . 75 %', ' 2 . 75 %'],
            'permutation_9' => [' 2.7 5 % ', ' 2.7 5 % '],
            'permutation_10' => ['-0.02', '-2%'],
            'permutation_11' => ['-0.02', ' -2% '],
            'permutation_12' => ['-0.02', '- 2% '],
            'permutation_13' => ['-0.02', '-2 % '],
            'permutation_14' => ['-0.0275', '-2.75% '],
            'permutation_15' => ['-0.0275', ' -2.75% '],
            'permutation_16' => ['-0.0275', '-2.75 % '],
            'permutation_17' => ['-0.0275', ' - 2.75 % '],
            'permutation_18' => ['0.02', '2%'],
            'permutation_19' => ['0.02', '% 2 '],
            'permutation_20' => ['0.02', ' %2 '],
            'permutation_21' => ['0.02', ' % 2 '],
            'permutation_22' => ['0.0275', '%2.75 '],
            'permutation_23' => ['0.0275', ' %2.75 '],
            'permutation_24' => ['0.0275', ' % 2.75 '],
            'permutation_25' => [' %2 . 75 ', ' %2 . 75 '],
            'permutation_26' => [' %2.7 5  ', ' %2.7 5  '],
            'permutation_27' => [' % 2 . 75 ', ' % 2 . 75 '],
            'permutation_28' => [' % 2.7 5  ', ' % 2.7 5  '],
            'permutation_29' => ['-0.0275', '-%2.75 '],
            'permutation_30' => ['-0.0275', ' - %2.75 '],
            'permutation_31' => ['-0.0275', '- % 2.75 '],
            'permutation_32' => ['-0.0275', ' - % 2.75 '],
            'permutation_33' => ['0.02', '2%'],
            'permutation_34' => ['0.02', '2 %'],
            'permutation_35' => ['0.02', ' 2%'],
            'permutation_36' => ['0.02', ' 2 % '],
            'permutation_37' => ['0.0275', '2.75%'],
            'permutation_38' => ['0.0275', ' 2.75 % '],
            'permutation_39' => ['2 . 75 % ', '2 . 75 % '],
            'permutation_40' => ['-0.0275', '-2.75% '],
            'permutation_41' => ['-0.0275', '- 2.75% '],
            'permutation_42' => ['-0.0275', ' - 2.75% '],
            'permutation_43' => ['-0.0275', ' -2.75 % '],
            'permutation_44' => ['-2. 75 % ', '-2. 75 % '],
            'permutation_45' => ['%', '%'],
            'permutation_46' => ['0.02', '%2 '],
            'permutation_47' => ['0.02', '% 2 '],
            'permutation_48' => ['0.02', ' %2 '],
            'permutation_49' => ['0.02', '% 2 '],
            'permutation_50' => ['0.02', ' % 2 '],
            'permutation_51' => ['0.02', ' 2 % '],
            'permutation_52' => ['-0.02', '-2%'],
            'permutation_53' => ['-0.02', '- %2'],
            'permutation_54' => ['-0.02', ' -%2 '],
            'permutation_55' => ['2%2', '2%2'],
            'permutation_56' => [' 2% %', ' 2% %'],
            'permutation_57' => [' % 2 -', ' % 2 -'],
            'permutation_58' => ['-0.02', '%-2'],
            'permutation_59' => ['-0.02', ' % - 2'],
            'permutation_60' => ['-0.0275', '%-2.75 '],
            'permutation_61' => ['-0.0275', ' % - 2.75 '],
            'permutation_62' => ['-0.0275', ' % - 2.75 '],
            'permutation_63' => ['-0.0275', ' % - 2.75 '],
            'permutation_64' => ['0.0275', ' % + 2.75 '],
            'permutation_65' => ['0.0275', ' % + 2.75 '],
            'permutation_66' => ['0.0275', ' % + 2.75 '],
            'permutation_67' => ['0.02', '+2%'],
            'permutation_68' => ['0.02', ' +2% '],
            'permutation_69' => ['0.02', '+ 2% '],
            'permutation_70' => ['0.02', '+2 % '],
            'permutation_71' => ['0.0275', '+2.75% '],
            'permutation_72' => ['0.0275', ' +2.75% '],
            'permutation_73' => ['0.0275', '+2.75 % '],
            'permutation_74' => ['0.0275', ' + 2.75 % '],
            'permutation_75' => ['-2.5E-6', '-2.5E-4%'],
            'permutation_76' => ['200', '2E4%'],
            'permutation_77' => ['-2.5E-8', '-%2.50E-06'],
            'permutation_78' => [' - % 2.50 E -06 ', ' - % 2.50 E -06 '],
            'permutation_79' => ['-2.5E-8', ' - % 2.50E-06 '],
            'permutation_80' => [' - % 2.50E- 06 ', ' - % 2.50E- 06 '],
            'permutation_81' => [' - % 2.50E - 06 ', ' - % 2.50E - 06 '],
            'permutation_82' => ['-2.5E-6', '-2.5e-4%'],
            'permutation_83' => ['200', '2e4%'],
            'permutation_84' => ['-2.5E-8', '-%2.50e-06'],
            'permutation_85' => [' - % 2.50 e -06 ', ' - % 2.50 e -06 '],
            'permutation_86' => ['-2.5E-8', ' - % 2.50e-06 '],
            'permutation_87' => [' - % 2.50e- 06 ', ' - % 2.50e- 06 '],
            'permutation_88' => [' - % 2.50e - 06 ', ' - % 2.50e - 06 '],
        ];
    }
}
