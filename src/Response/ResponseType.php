<?php

namespace Hrvoje\PhpFramework\Response;

enum ResponseType
{
    case Json;
    case EscapedHtml;
    case Plaintext;
}
