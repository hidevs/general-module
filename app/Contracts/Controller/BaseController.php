<?php

namespace Modules\General\Contracts\Controller;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

abstract class BaseController extends Controller
{
    use ValidatesRequests;
}
