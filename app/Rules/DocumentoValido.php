<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DocumentoValido implements Rule
{
    public function passes($attribute, $value)
    {
        // Remove tudo que não é número
        $numero = preg_replace('/\D/', '', $value);

        // CPF: 11 dígitos
        if (strlen($numero) === 11) {
            return $this->validarCPF($numero);
        }

        // CNPJ: 14 dígitos
        if (strlen($numero) === 14) {
            return $this->validarCNPJ($numero);
        }

        return false;
    }

    public function message()
    {
        return 'O :attribute deve ser um CPF ou CNPJ válido.';
    }

    private function validarCPF($cpf)
    {
        // Validação simples de CPF
        if (preg_match('/(\d)\1{10}/', $cpf)) return false;

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) return false;
        }

        return true;
    }

    private function validarCNPJ($cnpj)
    {
        // Validação simples de CNPJ
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) != 14) return false;

        if (preg_match('/(\d)\1{13}/', $cnpj)) return false;

        $tamanho = strlen($cnpj) - 2;
        $numeros = substr($cnpj, 0, $tamanho);
        $digitos = substr($cnpj, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;

        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += $numeros[$tamanho - $i] * $pos--;
            if ($pos < 2) $pos = 9;
        }

        $resultado = $soma % 11 < 2 ? 0 : 11 - $soma % 11;
        if ($resultado != $digitos[0]) return false;

        $tamanho++;
        $numeros = substr($cnpj, 0, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;
        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += $numeros[$tamanho - $i] * $pos--;
            if ($pos < 2) $pos = 9;
        }
        $resultado = $soma % 11 < 2 ? 0 : 11 - $soma % 11;

        return $resultado == $digitos[1];
    }
}
