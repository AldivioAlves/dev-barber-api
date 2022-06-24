<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\BarberAvailability;
use App\Models\BarberPhotos;
use App\Models\BarberServices;
use App\Models\BarberTestimonial;
use App\Models\UserAppointment;
use App\Models\UserFavorite;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BarberController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function show($id)
    {

        $barber = Barber::with([
            'photos',
            'testimonials',
            'availabilities',
            'services',
            'appointments' => function ($query) {
                $query->whereBetween('ap_datetime', [
                    date('Y-m-d') . ' 00:00:00',
                    date('Y-m-d', strtotime('+20 days')) . ' 23:59:59'
                ]);
            }
        ])->find($id);
        if (!$barber) {
            return $this->sendErrorResponse('Barbeiro não localizado', 404);
        }
        $favorited = false;
        $favoritedByUser = UserFavorite::where('barber_id', $barber->id)
            ->where('user_id', $this->loggedUser->id)->first();

        if ($favoritedByUser) {
            $favorited = true;
        }
        $barber['favorited'] =$favorited;

        $barber->avatar = url('media/avatars/' . $barber->avatar);
        foreach ($barber->photos as $photo) {
            $photo->url = url('media/uploads/' . $photo->url);
        }
        $availability = [];
        $availWeekdays = [];
        foreach ($barber->availabilities as $avaliabity) {
            $availWeekdays[$avaliabity->weekday] = explode(',', $avaliabity->hours);
        }
        $appointments = [];

        foreach ($barber->appointments as $appointment) {
            $appointments[] = $appointment->ap_datetime;
        }

        for ($q = 0; $q < 20; $q++) {
            $timeItem = strtotime('+' . $q . ' days');
            $weekday = date('w', $timeItem);
            if (in_array($weekday, array_keys($availWeekdays))) {
                $hours = [];
                $dayItem = date('Y-m-d', $timeItem);

                foreach ($availWeekdays[$weekday] as $hourItem) {
                    $dayFormated = $dayItem . ' ' . $hourItem . ':00';
                    if (!in_array($dayFormated, $appointments)) {
                        $hours[] = $hourItem;
                    }
                }
                if (count($hours) > 0) {
                    $availability[] = [
                        'date' => $dayItem,
                        'hours' => $hours
                    ];
                }
            }
        }

        $barber['available'] = $availability;

        return $barber;
    }

    public function index(Request $request)
    {
        $offset = $request->input('offset') ?? 0;

        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $city = $request->input('city');


        if (!empty('city')) {
            $res = $this->searchGeo($city);
            if (count($res['results']) > 0) {
                $lat = $res['results'][0]['geometry']['location']['lat'];
                $lng = $res['results'][0]['geometry']['location']['lng'];
            }
        } elseif (!empty($lat) && !empty($lng)) {
            $res = $this->searchGeo($lat . ',' . $lng);
            if (count($res['results']) > 0) {
                $city = $res['results'][0]['formatted_address'];
            }
        } else {
            $lat = '-23.5557714';
            $lng = '-46.6395571';
            $city = 'São Paulo';
        }

        $result = [];
        $barbers = Barber::select(Barber::raw('*, SQRT(POW(69.1 * (latitude - ' . floatval($lat) . '), 2) +
                                POW(69.1 * (' . floatval($lng) . ' - longitude) * COS(latitude / 57.3), 2)) AS distance'))
            ->havingRaw('distance < ?', [10])
            ->orderBy('distance', 'ASC')
            ->offset($offset)
            ->limit(5)
            ->get();

        if (!count($barbers)) {
            return $this->sendErrorResponse('Não existem barbeiros cadastrados', 404);
        }

        foreach ($barbers as $key => $value) {
            $barbers[$key]['avatar'] = url('media/avatars' . $barbers[$key]['avatar']);
        }

        $result['loc'] = 'São Paulo';
        $result['barbers'] = $barbers;

        return $this->sendResponse($result, 'Barbeiros retornados com sucesso');
    }

    public function searchGeo($address)
    {
        $address = urlencode($address);
        $key = env('MAPS_KEY', null);
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&key=' . $key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res, true);
    }

    public function createRandom()
    {
//        for($q=0; $q<15; $q++) {
//            $names = ['Boniek', 'Paulo', 'Pedro', 'Amanda', 'Leticia', 'Gabriel', 'Gabriela', 'Thais', 'Luiz', 'Diogo', 'José', 'Jeremias', 'Francisco', 'Dirce', 'Marcelo' ];
//            $lastnames = ['Santos', 'Silva', 'Santos', 'Silva', 'Alvaro', 'Sousa', 'Diniz', 'Josefa', 'Luiz', 'Diogo', 'Limoeiro', 'Santos', 'Limiro', 'Nazare', 'Mimoza' ];
//            $servicos = ['Corte', 'Pintura', 'Aparação', 'Unha', 'Progressiva', 'Limpeza de Pele', 'Corte Feminino'];
//            $servicos2 = ['Cabelo', 'Unha', 'Pernas', 'Pernas', 'Progressiva', 'Limpeza de Pele', 'Corte Feminino'];
//            $depos = [
//                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.',
//                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.',
//                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.',
//                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.',
//                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.'
//            ];
//            $newBarber = new Barber();
//            $newBarber->name = $names[rand(0, count($names)-1)].' '.$lastnames[rand(0, count($lastnames)-1)];
//            $newBarber->avatar = rand(1, 4).'.png';
//            $newBarber->stars = rand(2, 4).'.'.rand(0, 9);
//            $newBarber->latitude = '-23.5'.rand(0, 9).'30907';
//            $newBarber->longitude = '-46.6'.rand(0,9).'82759';
//            $newBarber->save();
//            $ns = rand(3, 6);
//            for($w=0;$w<4;$w++) {
//                $newBarberPhoto = new BarberPhotos();
//                $newBarberPhoto->barber_id = $newBarber->id;
//                $newBarberPhoto->url = rand(1, 5).'.png';
//                $newBarberPhoto->save();
//            }
//            for($w=0;$w<$ns;$w++) {
//                $newBarberService = new BarberServices();
//                $newBarberService->barber_id = $newBarber->id;
//                $newBarberService->name = $servicos[rand(0, count($servicos)-1)].' de '.$servicos2[rand(0, count($servicos2)-1)];
//                $newBarberService->price = rand(1, 99).'.'.rand(0, 100);
//                $newBarberService->save();
//            }
//            for($w=0;$w<3;$w++) {
//                $newBarberTestimonial = new BarberTestimonial();
//                $newBarberTestimonial->barber_id = $newBarber->id;
//                $newBarberTestimonial->name = $names[rand(0, count($names)-1)];
//                $newBarberTestimonial->rate = rand(2, 4).'.'.rand(0, 9);
//                $newBarberTestimonial->body = $depos[rand(0, count($depos)-1)];
//                $newBarberTestimonial->save();
//            }
//            for($e=0;$e<4;$e++){
//                $rAdd = rand(7, 10);
//                $hours = [];
//                for($r=0;$r<8;$r++) {
//                    $time = $r + $rAdd;
//                    if($time < 10) {
//                        $time = '0'.$time;
//                    }
//                    $hours[] = $time.':00';
//                }
//                $newBarberAvail = new BarberAvailability();
//                $newBarberAvail->barber_id = $newBarber->id;
//                $newBarberAvail->weekday = $e;
//                $newBarberAvail->hours = implode(',', $hours);
//                $newBarberAvail->save();
//            }
//        }
//        return $this->sendResponse([],'barbeiros criados com sucesso!');
    }
}
