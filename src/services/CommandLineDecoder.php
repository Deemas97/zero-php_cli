<?php
namespace App\Services;

include_once './src/core/ServiceInterface.php';
include_once './src/services/command_line_decoder/ParametersDecoded.php';

use App\Core\ServiceInterface;
use App\Services\CommandLineDecoder\ParametersDecoded;

class CommandLineDecoder implements ServiceInterface
{
    private ParametersDecoded $parametersDecoded;

    public function __construct(){}

    public function decodeFromArray(array $parametersInput): ?ParametersDecoded
    {
        $this->parametersDecoded = new ParametersDecoded();

        $argumentsMode = false;

        foreach ($parametersInput as $parameterInput) {
            if (!$this->validateInputParameter($parameterInput)) {
                return null;
            }

            $parameter = trim($parameterInput);

            if ($argumentsMode === false && $parameter === '--') {
                $argumentsMode = true;
                continue;
            }

            if ($argumentsMode) {
                $this->parametersDecoded->addArgument($this->tryConvertFromStringToNum($parameter));
            } else {
                if ($this->detectOption($parameter)) {
                    continue;
                }

                if ($this->detectFlags($parameter)) {
                    continue;
                }

                $this->parametersDecoded->addCommand($parameter);
            }
        }

        return $this->parametersDecoded;
    }

    public function decodeFromString(string $parametersInputStr): ?ParametersDecoded
    {
        return $this->decodeFromArray(explode(' ', str_replace('  ', ' ', trim($parametersInputStr))));
    }

    public function decodeFromKeyboard(): ?ParametersDecoded
    {
        return $this->decodeFromString(fread(STDIN, 255));
    }

    private function tryConvertFromStringToNum(string $value)
    {
        $valueConverted = $value;

        if (is_numeric($valueConverted)) {
            if ((int) $value == $valueConverted) {
                $valueConverted = intval($value);
            } elseif ((float) $value == $valueConverted) {
                $valueConverted = floatval($value);
            }
        }

        return $valueConverted;
    }

    private function validateInputParameter($parameter): bool
    {
        if (!is_scalar($parameter)) {
            print "[Services]: [CommandLineDecoder]: [Error]: caught non-scalar item of input parameters\n";
            return false;
        }

        return true;
    }

    private function detectOption($parameter): bool
    {
        if (str_starts_with($parameter, '--')) {
            $optionName = substr($parameter, 2);
            $optionValue = null;

            if (strpos($optionName, '=')) {
                $optionArray = explode('=', $optionName, 2);
                $optionName = trim($optionArray[0]);
                $optionValue = $this->tryConvertFromStringToNum(trim($optionArray[1]));
            }

            $this->parametersDecoded->addOption($optionName, (!empty($optionValue) ? $optionValue : true));
            return true;
        }

        return false;
    }

    private function detectFlags($parameter): bool
    {
        if (str_starts_with($parameter, '-')) {
            for ($i = 1; isset($parameter[$i]); $i++) {
                $this->parametersDecoded->addFlag($parameter[$i]);
            }
            return true;
        }

        return false;
    }
}
