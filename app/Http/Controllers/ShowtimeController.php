<?php

namespace App\Http\Controllers;

use App\Models\Showtime;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ShowtimeController extends Controller
{
    /**
     * Tüm seansları listele
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $showtimes = Showtime::with(['movie', 'cinemaHall.cinema'])
            ->where('start_time', '>=', now())
            ->where('start_time', '<=', now()->addDays(7))
            ->where('available_seats', '>', 0)
            ->orderBy('start_time')
            ->paginate(15);
        
        return view('showtimes.index', compact('showtimes'));
    }
    
 
    public function show($id)
    {
        $showtime = Showtime::with('movie')->findOrFail($id);
        
        $seatStatus = [];
        if ($showtime->seat_status) {
            $seatStatusStr = trim($showtime->seat_status, '"');
            $seatStatus = json_decode(stripslashes($seatStatusStr), true);
        }
        
        return view('showtimes.show', compact('showtime', 'seatStatus'));
    }
    
  
    public function selectSeats(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'error' => 'Bu işlemi gerçekleştirmek için giriş yapmalısınız.',
                'redirect' => route('login-user')
            ], 401);
        }
        
        Log::info('Koltuk seçimi isteği alındı', ['request' => $request->all()]);
        
        $request->validate([
            'seats' => 'required',
            'showtime_id' => 'required|exists:showtimes,id'
        ]);

        $seatsInput = $request->input('seats');
        $selectedSeats = is_string($seatsInput) ? json_decode($seatsInput, true) : (array) $seatsInput;
        
        Log::info('Seçilen koltuklar', ['seats' => $selectedSeats]);
        
        if (empty($selectedSeats)) {
            return response()->json(['error' => 'Lütfen en az bir koltuk seçin.'], 400);
        }
        
        $showtimeId = $request->input('showtime_id');
        
        $showtime = Showtime::findOrFail($showtimeId);
        
        try {
            $seatStatusStr = trim($showtime->seat_status, '"');
            $seatStatus = json_decode(stripslashes($seatStatusStr), true);
            
            if (!is_array($seatStatus)) {
                $seatStatus = [];
            }
            
            Log::info('Koltuk durumları', ['status' => $seatStatus]);
        } catch (\Exception $e) {
            Log::error('Koltuk bilgileri işlenirken hata', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Koltuk bilgileri işlenirken bir hata oluştu: ' . $e->getMessage()], 400);
        }
        
        $createdTickets = [];
        
        foreach ($selectedSeats as $seat) {
            if (isset($seatStatus[$seat]) && $seatStatus[$seat] !== 'available') {
                Log::warning('Koltuk müsait değil', ['seat' => $seat, 'status' => $seatStatus[$seat]]);
                return response()->json(['error' => $seat . ' numaralı koltuk artık müsait değil. Lütfen başka bir koltuk seçin.'], 400);
            }
            
            $seatStatus[$seat] = 'reserved';
            
            
            try {
                $ticketCode = sprintf(
                    "%s%s%04d%s",
                    strtoupper(substr($showtime->movie->title, 0, 2)), // Film adının ilk 2 harfi
                    date('ymd'),
                    mt_rand(1000, 9999), 
                    strtoupper(Str::random(2)) 
                );

                $ticketData = [
                    'user_id' => auth()->id(),
                    'showtime_id' => $showtimeId,
                    'seat_number' => $seat,
                    'price' => $showtime->price,
                    'status' => 'reserved',
                    'ticket_code' => $ticketCode,
                ];
                
                $ticket = Ticket::create($ticketData);
                
                $createdTickets[] = $ticket;
                Log::info('Bilet oluşturuldu', ['ticket' => $ticket]);
            } catch (\Exception $e) {
                Log::error('Bilet oluşturulurken hata', ['error' => $e->getMessage(), 'seat' => $seat]);
                return response()->json(['error' => 'Bilet oluşturulurken bir hata oluştu: ' . $e->getMessage()], 500);
            }
        }
        
        if (empty($createdTickets)) {
            Log::error('Hiç bilet oluşturulamadı');
            return response()->json(['error' => 'Bilet oluşturulamadı. Lütfen tekrar deneyin.'], 400);
        }
        
        try {
            $showtime->seat_status = '"' . addslashes(json_encode($seatStatus)) . '"';
            $showtime->available_seats = $showtime->available_seats - count($selectedSeats);
            $showtime->save();
            
            Log::info('Seans güncellendi', [
                'showtime_id' => $showtimeId,
                'new_seat_status' => $showtime->seat_status,
                'available_seats' => $showtime->available_seats
            ]);
        } catch (\Exception $e) {
            Log::error('Seans güncellenirken hata', ['error' => $e->getMessage()]);
            foreach ($createdTickets as $ticket) {
                $ticket->delete();
            }
            return response()->json(['error' => 'Seans güncellenirken bir hata oluştu: ' . $e->getMessage()], 500);
        }
        
        session([
            'success' => true,
            'showtime_id' => $showtimeId,
            'seats' => $selectedSeats,
            'tickets' => $createdTickets,
        ]);
        
        Log::info('İşlem başarılı, yönlendiriliyor', ['redirect' => route('tickets.confirmation')]);
        
        return response()->json([
            'message' => 'Biletiniz başarıyla oluşturuldu!',
            'redirect' => route('tickets.confirmation')
        ]);
    }
} 