<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Runtime;

/**
 * Defines a routable class
 *
 * Routable is an empty interface. Its sole purpose is to identify
 * routable classes at compile time.
 *
 * Pulic methods annotated with the `@route` annotation are treated as
 * individual routes through the application. There are no fixed rules
 * concerning how these routes should be group together. It is recommended
 * however to let each class represent a web resource, and let the individual
 * routes represent actions on the resource (eg. GET, POST and so forth).
 *
 * A route is executed with an Environment as argument. The environment can be
 * used to access the inroute runtime. Se the Environment class for more
 * information.
 *
 * The route may write directly to standard output. A more testable approch is
 * to let the route retrun some kind of value object. The return value of the
 * route will be the return value of the router.
 *
 * If the route concludes that control should be passed to next executable route
 * a NextRouteException can be thrown.
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
interface Routable
{
}
