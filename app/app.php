<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/car.php";

    session_start();
    if(empty($_SESSION['list_of_cars'])) {
        $firstCar = new Car("Tesla X", 100000, 0, "pictures/tesla.jpg");
        $secondCar = new Car("Honda Accord", 10000, 50000, "pictures/honda.jpg");
        $thirdCar = new Car("Ferrari Enzo", 350000, 15000, "pictures/ferrari-enzo.jpg");
        $fourthCar = new Car("Toyota Corolla", 6000, 100000, "pictures/toyota-corolla.jpg");
        $fifthCar = new Car("Mitsubishi Lancer", 20000, 100, "pictures/mitsubishi-lancer.jpg");
        $allCars = array($firstCar, $secondCar, $thirdCar, $fourthCar, $fifthCar);
        $_SESSION['list_of_cars'] = $allCars;
    }
    $app = new Silex\Application();
    $app['debug'] = true;
    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views'
    ));
    $app->get("/", function() use ($app) {
        return $app['twig']->render('dealership_search.html.twig');

    });
    $app->get("/view_cars", function() use ($app) {
        $totalCars = Car::getAll();
        $matchingCars = array();
        $max_price = $_GET["max_price"];
        $max_miles = $_GET["max_mileage"];
        foreach($totalCars as $car){
            $price = $car->getPrice();
            $mileage = $car->getMiles();
            if(($price <= $max_price) && ($mileage <= $max_miles)) {
              array_push($matchingCars, $car);
            }
        }
        return $app['twig']->render('results.html.twig', array('matchedCars' => $matchingCars));
    });

    $app->get("/car_form", function() use ($app) {
        return $app['twig']->render('car_form.html.twig');
    });

    $app->post("/post_car", function() use ($app) {
        $cars = new Car($_POST['carModel'], $_POST['carPrice'], $_POST['carMiles'], $_POST['carImage']);
        $cars->save();
        return $app['twig']->render('added_car.html.twig');
    });
    return $app;
?>
