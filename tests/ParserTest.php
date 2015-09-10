<?php
namespace Ayeo\Parser\Test;

//todo: simplify imports (mocks)
use Ayeo\Parser\Parser;
use Ayeo\Parser\Test\Mock\Customer;
use Ayeo\Parser\Test\Mock\ObjectWithPrivateCustomer;
use Ayeo\Parser\Test\Mock\ObjectWithPublicCustomer;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testTest()
    {
        $parser = new Parser();

        $subject = new Customer();
        $subject->name = 'Harry';

        $parsedBody = $parser->parse('Hi {{subject.name}}!', ['subject' => $subject]);

        $this->assertEquals('Hi Harry!', $parsedBody);
    }

    public function testNested()
    {
        $parser = new Parser();

        $customer = new Customer();
        $customer->name = 'Harry';

        $order = new ObjectWithPublicCustomer();
        $order->customer = $customer;

        $parsedBody = $parser->parse('Hi {{order.customer.name}}!', ['order' => $order]);
        $this->assertEquals('Hi Harry!', $parsedBody);
    }

    public function testGetter()
    {
        $parser = new Parser();

        $customer = new Customer();
        $customer->name = 'Harry';

        $order = new ObjectWithPrivateCustomer($customer);

        $parsedBody = $parser->parse('Hi {{order.customer.name}}!', ['order' => $order]);
        $this->assertEquals('Hi Harry!', $parsedBody);
    }

    public function testNestedArray()
    {
        $parser = new Parser();
        $data = ['user' =>  ['supervisor' => ['name' => 'Harry']]];
        $parsedBody = $parser->parse('Hi {{user.supervisor.name}}!', $data);
        $this->assertEquals('Hi Harry!', $parsedBody);
    }

    /**
     * @expectedException           \Exception
     * @expectedExceptionMessage    Class stdClass has not property or getter for: child. Full path: parent.child.name
     */
    public function testStdClass()
    {
        $parser = new Parser();

        $child = new \stdClass();
        $child->name = 'Harry';

        $parent = new \stdClass();
        $parent->child = $child;

        $parsedBody = $parser->parse('Hi {{parent.child.name}}!', ['parent' => $parent]);
    }


    /**
     * @expectedException           \Exception
     * @expectedExceptionMessage    Null value for: customer.name
     */
    public function testNull()
    {
        $parser = new Parser();
        $parsedBody = $parser->parse('Hi {{customer.name}}!', ['customer' => new Customer()]);
        $this->assertEquals('Hi Harry!', $parsedBody);
    }

    public function testSimpleValueInArray()
    {
        $parser = new Parser();
        $result = $parser->parse('Hi, my name is {{name}}.', ['name' => 'Nikola Tesla']);
        $this->assertEquals('Hi, my name is Nikola Tesla.', $result);
    }

    public function testPrefix()
    {
        $parser = new Parser('prefix.');

        $customer = new Customer();
        $customer->name = 'Nikola Tesla';

        $order = new ObjectWithPublicCustomer();
        $order->customer = $customer;

        $template = "{{customer.name}} is great inventor!";
        $parsedBody = $parser->parse($template, ['prefix' => $order]);
        $this->assertEquals('Nikola Tesla is great inventor!', $parsedBody);
    }

    //todo: test undefinied index!
    //todo: multi occurence of same pattern


}