# LiquidMetal

**This is a PHP port of [rmm5t/liquidmetal](http://github.com/rmm5t/liquidmetal):**

“A mimetic poly-alloy of the Quicksilver scoring algorithm, essentially
LiquidMetal. `</Schwarzenegger Voice>`”

## Usage

If not using Composer's autoloader, include the library:

    include 'LiquidMetal.php';

Score any string against an abbreviation:

    LiquidMetal::score("FooBar",  "foo");   //=> 0.950
    LiquidMetal::score("FooBar",  "fb");    //=> 0.917
    LiquidMetal::score("Foo Bar", "fb");    //=> 0.929
    LiquidMetal::score("Foo Bar", "baz");   //=> 0.0
    LiquidMetal::score("Foo Bar", "");      //=> 0.8

All scores fall between a range of 0.0 (no match) to 1.0 (perfect match).
