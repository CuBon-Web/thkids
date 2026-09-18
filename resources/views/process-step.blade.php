@extends('layouts.main.master')
@section('title')
Quy trình làm việc | {{ $setting->company }}
@endsection
@section('description')
{{ $setting->description ?? $setting->company }}
@endsection
@section('image')
@if (!empty($banner) && isset($banner[0]))
{{ url('' . $banner[0]->image) }}
@else
{{ url('frontend/images/page-header-bg.jpg') }}
@endif
@endsection
@section('js')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var blocks = document.querySelectorAll('.process-step-desc-wrap');
    blocks.forEach(function (block) {
      var text = block.querySelector('.process-step-desc');
      var toggle = block.querySelector('.process-step-toggle');
      if (!text || !toggle) return;

      if (text.scrollHeight <= text.clientHeight + 2) {
        toggle.style.display = 'none';
        return;
      }

      toggle.addEventListener('click', function () {
        var expanded = text.classList.toggle('is-expanded');
        toggle.textContent = expanded ? 'View less' : 'View more';
      });
    });
  });
</script>
@endsection
@section('css')
<style>
  .process-step-desc-wrap .process-step-desc {
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .process-step-desc-wrap .process-step-desc.is-expanded {
    display: block;
    -webkit-line-clamp: unset;
    overflow: visible;
  }

  .process-step-desc-wrap .process-step-toggle {
    margin-top: 8px;
    padding: 0;
    border: 0;
    background: transparent;
    color: var(--theme-color1);
    font-size: 14px;
    font-weight: 600;
    line-height: 1.2;
    cursor: pointer;
  }
</style>
@endsection
@section('content')
<section class="page-title">
    <div class="auto-container">
      <div class="title-outer text-center"> <div class="h1 title">Process Step</div>
        <ul class="page-breadcrumb">
          <li><a href="{{route('home')}}">Home</a></li>
          <li>Process Step</li>
        </ul>
      </div>
    </div>
  </section>
  <section class="working-section">
    <div class="outer-box">
      <div class="bg bg-image"><img src="/frontend/images/work-bg-1.png" alt=""></div>
      <div class="auto-container">
        <div class="sec-title-box">
          <div class="sec-title">
            <div class="h6 sub-title">How We Work</div>
            <div class="h2 title">We produce high quality products using specialized sawdust charcoal production equipment.</div>
          </div>
        </div>
        <div class="working-outer">
          @php
            $stepOrdinals = ['One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten'];
          @endphp
          @forelse ($processSteps ?? [] as $step)
            @php
                $stepLabel = 'Step ' . ($stepOrdinals[$loop->index] ?? $loop->iteration);
            @endphp
          <div class="working-block">
            <div class="inner-block">
              <div class="count">{{ $stepLabel }}</div>
              @if ($step->image)
              <div class="image">
                <img src="{{ url($step->image) }}" alt="{{ $step->title }}">
              </div>
              @endif
              <div class="content">
                <div class="h4 title">{{ $step->title }}</div>
                @if ($step->description)
                <div class="process-step-desc-wrap">
                  <div class="text process-step-desc">{!! languageName($step->description) !!}</div>
                  <button type="button" class="process-step-toggle">View more</button>
                </div>
                @endif
              </div>
              <div class="icon"><i class="fa fa-arrow-right-long"></i></div>
            </div>
          </div>
          @empty
          <p class="text-center mb-0">Chưa có dữ liệu quy trình. Vui lòng cập nhật trong trang quản trị.</p>
          @endforelse
        </div>
      </div>
    </div>
  </section>
@endsection