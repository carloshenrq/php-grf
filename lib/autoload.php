<?php
/**
 * MIT License
 * 
 * Copyright (c) 2019 Carlos Henrique
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 * Classe para realizar o autoloading dos dados.
 *
 */
final class Autoload
{
    /**
     * Método para registrar o autoload.
     */
    public static function register()
    {
        // Registra as funções de autoload para que se tenha
        // o funcionamento das classes do framework.
        spl_autoload_register([
            'Autoload',
            'loader'
        ], true, false);
    }

    /**
     * Método para carregar as classes do framework.
     *
     * @param string $className Nome da classe que irá ser carregada
     */
    public static function loader($className)
    {
        // Monta o nome do arquivo da classe e logo após
        // Tenta fazer a inclusão do arquivo
        $classFile = join(DIRECTORY_SEPARATOR, [
            __DIR__,
            $className . '.php'
        ]);
        $classFile = str_replace('\\', DIRECTORY_SEPARATOR, $classFile);

        // Verifica se o arquivo existe se existir inclui o arquivo no código
        if(file_exists($classFile))
            require_once $classFile;
    }
}

// Registra o autoload para o site.
Autoload::register();
