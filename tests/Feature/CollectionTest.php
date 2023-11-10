<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEqualsCanonicalizing;

class CollectionTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = collect([1,2,3]);
        $this->assertEqualsCanonicalizing([1,2,3], $collection->all());
    }

    public function testForEach()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        foreach ($collection as $key => $value) {
            $this->assertEquals($key +1, $value);
        }
    }

    public function testCRUD()
    {
        $collection = collect([]);
        $collection->push(1,2,3);
        $this->assertEqualsCanonicalizing([1,2,3], $collection->all());

        $result = $collection->pop();
        $this->assertEquals(3, $result);
        $this->assertEqualsCanonicalizing([1,2], $collection->all());
    }

    public function testMap()
    {
        $collection = collect([1,2,3]);
        $result = $collection->map(function($item){
            return $item * 2;
        });
        $this->assertEqualsCanonicalizing([2,4,6], $result->all());
    }

    public function testMapInto()
    {
        $collection = collect(["Eko"]);
        $result = $collection->mapInto(Person::class);
        $this->assertEquals([new Person("Eko")], $result->all());
    }

    public function testMapSpread()
    {
        $collection = collect([
            ["Eko", "Kurniawan"],
            ["Kurniawan", "Khannedy"]
        ]);

        $result = $collection->mapSpread(function($fistName, $lastName){
            $fullName = $fistName . ' ' . $lastName;
            return new Person($fullName);
        });

        $this->assertEquals([
            new Person("Eko Kurniawan"),
            new Person("Kurniawan Khannedy"),
        ], $result->all());
    }

    public function testMapToGroups()
    {
        $collection = collect([
            [
                "name" => "Eko",
                "departement" => "IT"
            ],
            [
                "name" => "Khannedy",
                "departement" => "IT"
            ],
            [
                "name" => "Budi",
                "departement" => "HR"
            ],
        ]);

        $result = $collection->mapToGroups(function ($person) {
            return [
                $person["departement"] => $person["name"]
            ];
        });

        $this->assertEquals([
            "IT" => collect(["Eko", "Khannedy"]),
            "HR" => collect(["Budi"])
        ], $result->all());
    }

    public function testZip()
    {
        $collection1 = collect([1,2,3]);
        $collection2 = collect([4,5,6]);
        $collection3 = $collection1->zip($collection2);

        $this->assertEqualsCanonicalizing([
            collect([1,4]),
            collect([2,5]),
            collect([3,6])
        ], $collection3->all());
    }

    public function testConcat()
    {
        $collection1 = collect([1,2,3]);
        $collection2 = collect([4,5,6]);
        $collection3 = $collection1->concat($collection2);

        $this->assertEqualsCanonicalizing([1,2,3,4,5,6], $collection3->all());
    }

    public function testCombine()
    {
        $collection1 = collect(["name", "address"]);
        $collection2 = collect(["Eko", "Indonesia"]);
        $collection3 = $collection1->combine($collection2);

        $this->assertEqualsCanonicalizing([
            "name" => "Eko",
            "address" => "Indonesia"
        ], $collection3->all());
    }

    public function testCollapse()
    {
        $collection = collect([
            [1,2,3],
            [4,5,6],
            [7,8,9]
        ]);

        $result = $collection->collapse();
        $this->assertEqualsCanonicalizing([1,2,3,4,5,6,7,8,9], $result->all());

    }

    public function testFlatMap()
    {
        $collection = collect([
            [
                "name" => "Eko",
                "hobbies" => ["Coding", "Gaming"]
            ],
            [
                "name" => "Kurniawan",
                "hobbies" => ["Reading", "Writing"]
            ]
            ]);

        $hobbies =$collection->flatMap(function($item){
            return $item["hobbies"];
        });

        assertEqualsCanonicalizing(["Coding", "Gaming", "Reading", "Writing"], $hobbies->all());
    }

    public function testStringRepresentation()
    {
        $collection = collect(["Eko", "Khannedy", "Khannedy"]);

        $this->assertEquals("Eko-Khannedy-Khannedy", $collection->join(glue:"-"));
        $this->assertEquals("Eko-Khannedy_Khannedy", $collection->join(glue:"-", finalGlue:"_"));
        $this->assertEquals("Eko, Khannedy and Khannedy", $collection->join(glue:", ", finalGlue:" and "));
    }

    public function testFilter()
    {
        $collection = collect([
            "Eko" => 100,
            "Budi" => 80,
            "Joko" => 90
        ]);

        $result = $collection->filter(function($value, $key){
            return $value >= 90;
        });

        $this->assertEquals([
            "Eko" => 100,
            "Joko" => 90
        ], $result->all());
    }

    public function testFilterIndex()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);
        $result = $collection->filter(function($value, $key){
            return $value % 2 == 0;
        });

        $this->assertEqualsCanonicalizing([2,4,6,8,10], $result->all());
    }

    public function testPartition()
    {
        $collection = collect([
            "Eko" => 100,
            "Budi" => 80,
            "Joko" => 90
        ]);

        [$result1, $result2] = $collection->partition(function($value, $key){
            return $value >= 90;
        });

        $this->assertEquals([
            "Eko" => 100,
            "Joko" => 90
        ], $result1->all());

        $this->assertEquals([
            "Budi" => 80
        ], $result2->all());
    }

    public function testTesting()
    {
        $collection = collect(["Eko", "Kurniawan", "Khannedy"]);
        self::assertTrue($collection->contains("Eko"));
        self::assertTrue($collection->contains(function($value, $key){
            return $value === "Khannedy";
        }));
    }
}
