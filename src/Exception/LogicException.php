<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Exception;

/**
 * Represents an error in the program logic, should lead directly to a fix
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class LogicException extends \LogicException implements \inroute\Exception
{
}
