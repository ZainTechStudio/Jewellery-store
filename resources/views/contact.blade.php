@extends('layouts.app')

@section('title', 'Contact & Boutiques — Speak to a Goldsmith | AURELIA')
@section('meta_description', 'Contact AURELIA client care, book a private atelier appointment, or visit our boutiques in Paris, New York and Dubai.')
@section('og_title', 'Contact & Boutiques | AURELIA')
@section('og_description', 'Book a private atelier appointment or reach AURELIA client care.')

@section('content')

<section class="page-head">
  <div class="container">
    <p class="eyebrow">Client Care</p>
    <h1>Get in Touch</h1>
    <div class="rule"></div>
    <p class="breadcrumb-lux"><a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; Contact</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-7" data-aos="fade-right">
        <p class="eyebrow">Write to us</p>
        <h2 class="display-6 mt-2">A goldsmith will reply</h2>
        <div class="rule rule-start"></div>
        <form class="row g-3 mt-2" data-fake="Message sent — we reply within 24 hours" method="POST">
          @csrf
          {{-- action left unset for now — contact endpoint is wired up in a later phase --}}
          <div class="col-md-6"><input class="form-control form-control-lux" name="first_name" placeholder="First name" required></div>
          <div class="col-md-6"><input class="form-control form-control-lux" name="last_name" placeholder="Last name" required></div>
          <div class="col-md-6"><input type="email" class="form-control form-control-lux" name="email" placeholder="Email address" required></div>
          <div class="col-md-6"><input type="tel" class="form-control form-control-lux" name="phone" placeholder="Phone (optional)"></div>
          <div class="col-12">
            <select class="form-select form-select-lux" name="reason">
              <option>Reason: General enquiry</option>
              <option>Reason: Private appointment</option>
              <option>Reason: Bespoke commission</option>
              <option>Reason: Repair &amp; care</option>
            </select>
          </div>
          <div class="col-12"><textarea rows="5" class="form-control form-control-lux" name="message" placeholder="Your message" required></textarea></div>
          <div class="col-12"><button class="btn btn-gold" type="submit">Send message</button></div>
        </form>
      </div>

      <div class="col-lg-5" data-aos="fade-left">
        <div class="summary-box">
          <p class="eyebrow mb-3">Boutiques</p>
          <div class="mb-4">
            <h5 class="mb-1">Paris — Atelier</h5>
            <p class="text-muted-lux mb-0" style="font-size:.88rem">14 Rue de la Paix, 75002<br>Mon–Sat, 10:00–19:00</p>
          </div>
          <div class="mb-4">
            <h5 class="mb-1">New York</h5>
            <p class="text-muted-lux mb-0" style="font-size:.88rem">728 Fifth Avenue<br>Mon–Sat, 10:00–18:00</p>
          </div>
          <div class="mb-4">
            <h5 class="mb-1">Dubai</h5>
            <p class="text-muted-lux mb-0" style="font-size:.88rem">Dubai Mall, Fashion Avenue<br>Daily, 10:00–22:00</p>
          </div>
          <hr style="border-color:var(--line)">
          <p class="text-muted-lux mb-1"><i class="bi bi-telephone text-gold me-2"></i>+1 (212) 555 0148</p>
          <p class="text-muted-lux mb-3"><i class="bi bi-envelope text-gold me-2"></i>care@aurelia.com</p>
          <div>
            <a class="social-ico" href="#"><i class="bi bi-instagram"></i></a>
            <a class="social-ico" href="#"><i class="bi bi-whatsapp"></i></a>
            <a class="social-ico" href="#"><i class="bi bi-pinterest"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
