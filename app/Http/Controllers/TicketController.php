<?php

namespace App\Http\Controllers;

use App\Models\Showtime;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
 
    public function confirmation()
    {
        if (!session('success')) {
            return redirect()->route('home');
        }
        
        $showtimeId = session('showtime_id');
        $seats = session('seats');
        $tickets = session('tickets');
        
        $showtime = Showtime::with(['movie', 'cinemaHall.cinema'])->findOrFail($showtimeId);
        
        $isLoggedIn = auth()->check();
        $userId = auth()->id();
        
        return view('tickets.confirmation', compact('showtime', 'seats', 'tickets', 'isLoggedIn', 'userId'));
    }
  
    public function show($code)
    {
        $ticket = Ticket::with(['showtime.movie', 'showtime.cinemaHall.cinema'])
            ->where('ticket_code', $code)
            ->first();
        
        if (!$ticket) {
            return redirect()->route('home')->with('error', 'Bilet bulunamadı.');
        }
        
        return view('tickets.show', compact('ticket'));
    }
} 