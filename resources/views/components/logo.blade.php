@if($image)
    {{-- Include svgs inline so they can use currentColor for light/dark mode, but only if they are hosted on the same server (browser will reject it otherwise) --}}
    @if($is_svg)
        <svg {{ $attributes->class(['tw:dark:text-white', 'tw:text-gray-600']) }}>
            <use href="{{ asset($image) }}"></use>
        </svg>
    @else
        <img {{ $attributes }} src="{{ asset($image) }}" alt="{{ $text }}">
    @endif
@else
    <svg {{ $attributes->class(['tw:dark:text-white', 'tw:text-gray-600'])->class($responsive ? ['tw:hidden', $logo_show_class] : []) }}
     xmlns="http://www.w3.org/2000/svg" xml:space="preserve" viewBox="0 0 170 36"> <style>.st0{fill:#fff}</style>
    <path d="M28.26 2.71c9.65 0 15.68 6.42 15.68 16.07 0 9.7-6.02 16.12-15.68 16.12s-15.63-6.42-15.63-16.12c0-9.65 5.98-16.07 15.63-16.07zm0 27.1c6.33 0 10.14-4.25 10.14-11.03 0-6.73-3.81-10.98-10.14-10.98-6.38 0-10.1 4.25-10.1 10.98.01 6.78 3.73 11.03 10.1 11.03zM51.3 3.29v11.03c1.06-1.37 3.32-2.52 6.51-2.52 6.46 0 11.07 4.61 11.07 11.47 0 6.82-4.61 11.38-11.34 11.38-6.47 0-11.56-4.25-11.56-11.38V3.29h5.32zm6.11 13.37c-3.68 0-6.11 2.44-6.11 6.55s2.44 6.55 6.11 6.55c3.72 0 6.15-2.44 6.15-6.55s-2.43-6.55-6.15-6.55zM69.76 12.15h17.71v3.54L77.2 29.42h10.27v4.87H69.76v-3.54l10.32-13.73H69.76v-4.87zM99.88 11.84c6.86 0 11.42 4.56 11.42 11.38 0 6.86-4.56 11.47-11.42 11.47s-11.47-4.61-11.47-11.47c0-6.82 4.61-11.38 11.47-11.38zm-.05 17.97c3.72 0 6.16-2.44 6.16-6.55s-2.44-6.55-6.16-6.55c-3.67 0-6.11 2.43-6.11 6.55s2.44 6.55 6.11 6.55zM124.33 11.75v4.87c-3.54 0-5.67 1.73-5.67 5.27v12.4h-5.31v-12.4c-.01-6.73 4.25-10.14 10.98-10.14zM147.49 23.3v10.98h-5.31v-2.12c-1.06 1.37-3.32 2.52-6.55 2.52-6.42 0-11.03-4.61-11.03-11.47 0-6.82 4.61-11.38 11.42-11.38 6.86.01 11.47 4.61 11.47 11.47zm-11.43-6.59c-3.72 0-6.16 2.43-6.16 6.55s2.44 6.55 6.16 6.55c3.67 0 6.11-2.44 6.11-6.55s-2.43-6.55-6.11-6.55zM153.42 28.18c1.95 0 3.28 1.28 3.28 3.23 0 1.99-1.33 3.28-3.28 3.28-1.95 0-3.28-1.28-3.28-3.28.01-1.95 1.34-3.23 3.28-3.23z" class="st0"/><linearGradient id="SVGID_1_" x1="12.617" x2="43.928" y1="18.512" y2="18.512" gradientUnits="userSpaceOnUse"><stop offset="0" style="stop-color:#c100e9"/><stop offset=".041" style="stop-color:#c402e0"/><stop offset=".108" style="stop-color:#ca06c6"/><stop offset=".194" style="stop-color:#d50e9d"/><stop offset=".295" style="stop-color:#e51863"/><stop offset=".408" style="stop-color:#f8251a"/><stop offset=".446" style="stop-color:#ff2a00"/><stop offset=".731" style="stop-color:#d47c00"/><stop offset=".994" style="stop-color:#7600ff"/></linearGradient><path d="M28.25 2.41c9.65 0 15.68 6.42 15.68 16.08 0 9.7-6.02 16.12-15.68 16.12s-15.63-6.42-15.63-16.12c0-9.66 5.98-16.08 15.63-16.08zm0 27.11c6.33 0 10.14-4.25 10.14-11.03 0-6.73-3.81-10.98-10.14-10.98-6.38 0-10.1 4.25-10.1 10.98 0 6.78 3.72 11.03 10.1 11.03z" style="fill:url(#SVGID_1_)"/><linearGradient id="SVGID_00000011731127794000669250000006731803297857590429_" x1="88.308" x2="111.352" y1="22.986" y2="22.986" gradientUnits="userSpaceOnUse"><stop offset="0" style="stop-color:#c100e9"/><stop offset=".041" style="stop-color:#c402e0"/><stop offset=".108" style="stop-color:#ca06c6"/><stop offset=".194" style="stop-color:#d50e9d"/><stop offset=".295" style="stop-color:#e51863"/><stop offset=".408" style="stop-color:#f8251a"/><stop offset=".446" style="stop-color:#ff2a00"/><stop offset=".731" style="stop-color:#d47c00"/><stop offset=".994" style="stop-color:#7600ff"/></linearGradient><path d="M99.85 11.56c6.91 0 11.5 4.56 11.5 11.38 0 6.86-4.59 11.47-11.5 11.47S88.31 29.8 88.31 22.94c0-6.82 4.63-11.38 11.54-11.38zm-.04 17.98c3.74 0 6.2-2.44 6.2-6.55 0-4.12-2.45-6.55-6.2-6.55-3.7 0-6.15 2.44-6.15 6.55 0 4.11 2.45 6.55 6.15 6.55z" style="fill:url(#SVGID_00000011731127794000669250000006731803297857590429_)"/></svg>
    @if($responsive)
    <svg {{ $attributes->class(['tw:dark:text-white', 'tw:text-gray-600', 'tw:inline-block', $logo_hide_class]) }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 77.558 77.558">
 <defs>
    <pattern id="gradientPattern" patternUnits="userSpaceOnUse" width="100" height="100">
      <image href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNncmFkaWVudCkiLz48ZGVmcz48bGluZWFyR3JhZGllbnQgaWQ9ImdyYWRpZW50IiB4MT0iMCIgeTE9IjAiIHgyPSIxMDAiIHkyPSIwIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHN0b3Agb2Zmc2V0PSIwIiBzdG9wLWNvbG9yPSIjYzEwMGU5Ii8+PHN0b3Agb2Zmc2V0PSIwLjA0MSIgc3RvcC1jb2xvcj0iI2M0MDJlMCIvPjxzdG9wIG9mZnNldD0iMC4xMDgiIHN0b3AtY29sb3I9IiNjYTA2YzYiLz48c3RvcCBvZmZzZXQ9IjAuMTk0IiBzdG9wLWNvbG9yPSIjZDUwZTlkIi8+PHN0b3Agb2Zmc2V0PSIwLjI5NSIgc3RvcC1jb2xvcj0iI2U1MTg2MyIvPjxzdG9wIG9mZnNldD0iMC40MDgiIHN0b3AtY29sb3I9IiNmODI1MWEiLz48c3RvcCBvZmZzZXQ9IjAuNDQ2IiBzdG9wLWNvbG9yPSIjZmYyYTAwIi8+PHN0b3Agb2Zmc2V0PSIwLjczMSIgc3RvcC1jb2xvcj0iI2Q0N2MwMCIvPjxzdG9wIG9mZnNldD0iMC45OTQiIHN0b3AtY29sb3I9IiM3NjAwZmYiLz48L2xpbmVhckdyYWRpZW50PjwvZGVmcz48L3N2Zz4=" width="100" height="100" />
    </pattern>
  </defs>

  <!-- Transformed path to fill the viewBox more -->
  <g transform="translate(8, 8) scale(1.8)">
    <path fill="url(#gradientPattern)"
          d="M19.96 2.4c10.7 0 17.38 7.12 17.38 17.82 0 10.75-6.68 17.87-17.38 17.87S2.63 30.97 2.63 20.22C2.63 9.52 9.25 2.4 19.96 2.4zm0 30.04
             c7.02 0 11.24-4.71 11.24-12.22 0-7.46-4.22-12.17-11.24-12.17-7.07 0-11.19 4.71-11.19 12.17-.01 7.51 4.12 12.22 11.19 12.22z" />
  </g>

    </svg>
    @endif
@endif
