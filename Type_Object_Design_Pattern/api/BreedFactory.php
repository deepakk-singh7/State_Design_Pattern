<?php

require_once 'Breed.php'; 

/**
 * A factory class responsible for creating Breed objects.
 *
 * This class centralizes the logic for converting raw data (from JSON) into
 * a well-formed Breed object.
 */

// class BreedFactory {
    /**
     * Creates a Breed object from a name and a raw data array.
     *
     * @param string $name The name of the breed.
     * @param array $data The associative array of data from monsters.json.
     * @return Breed The newly created Breed object.
     */
//     public static function create(string $name, array $data): Breed {
//         $health = $data['health'] ?? 0;
//         $attack = $data['attack'] ?? '';
//         $image = $data['image'] ?? '';

//         return new Breed($name, $health, $attack, $image);
//     }
// }

// -------------------- modification ---------------

/**
 * A factory class responsible for creating Breed objects.
 *
 * Use Reflection to automatically map data from an array
 * onto the properties of the Breed class.
 */
class BreedFactory {

    private const NAME_PROP = 'name';
    /**
     * Creates a Breed object by "hydrating" it from a raw data array.
     *
     * @param string $name The name of the breed.
     * @param array $data The associative array of data from monsters.json.
     * @return Breed The newly created Breed object.
     */
    // public static function create(string $name, array $data): Breed {
    //     //  create a "ReflectionClass" instance from our Breed class.
    //     //    This allows us to inspect the class's structure at runtime.
    //     $reflectionClass = new ReflectionClass(Breed::class);
        
    //     // create a new instance of the Breed class without calling its constructor yet.
    //     //    This gives us an empty shell to fill with data.
    //     $breedInstance = $reflectionClass->newInstanceWithoutConstructor();
        
    //     // manually set the 'name' since it's not part of the inner data array.
    //     $breedInstance->name = $name;

    //     // iterate over all the PUBLIC properties defined in the Breed class.
    //     foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
    //         $propertyName = $property->getName();

    //         // check if a key with the same name exists in our incoming data.
    //         if (isset($data[$propertyName])) {
    //             // if it exists, set the value on our new object instance.
    //             $property->setValue($breedInstance, $data[$propertyName]);
    //         }
    //     }

    //     return $breedInstance;
    // }


    // --- Modification  ----

    /**
     * Creates a Breed object by passing the data array directly to the constructor.
     *
     * @param string $name The name of the breed.
     * @param array $data The associative array of data from monsters.json.
     * @return Breed The newly created Breed object.
     */
    public static function create(string $name, array $data): Breed {
        // add the 'name' to the data array.
        $data[self::NAME_PROP] = $name;

        // Use the spread operator (...) to turn the associative array into named arguments for the constructor.
        return new Breed(...$data);
    }
}
