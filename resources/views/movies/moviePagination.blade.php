{{-- Display pagination --}}
@if($numPages > 1)
    {{-- Pagination for small screen sizes only --}}
    <div class="visible-xs visible-sm text-center">
        <ul class="pagination">
            @if($results['page'] > 1)
                <li>
                    <a href="/findMovie?search={{ $search }}&type={{ $type }}&page={{ $results['page'] - 1 }}">prev</a>
                </li>
            @endif
                <li class="active"><a href="#">{{ $results['page'] }}</a></li>
            @if($results['page'] < $numPages)
                <li>
                    <a href="/findMovie?search={{ $search }}&type={{ $type }}&page={{ $results['page'] + 1 }}">next</a>
                </li>
            @endif
        </ul>
    </div>

    {{-- Pagination other screen sizes --}}
    <div class="hidden-xs hidden-sm text-center">
        <ul class="pagination">
            {{-- Show previous page link --}}
            @if($results['page'] > 1)
                <li>
                    <a href="/findMovie?search={{ $search }}&type={{ $type }}&page={{ $results['page'] - 1 }}">prev</a>
                </li>
            @endif

            {{-- Show page links --}}
            @if($numPages <= 10)
                @for($ii = 1;$ii <= $numPages;$ii++)
                    <li{{ ($results['page']==$ii) ? ' class=active' : '' }}>
                        <a href="/findMovie?search={{ $search }}&type={{ $type }}&page={{ $ii }}">{{ $ii }}</a>
                    </li>
                @endfor
            @elseif($results['page'] <= 6)
                @for($ii = 1;$ii <= 10; $ii++)
                    <li{{ ($results['page']==$ii) ? ' class=active' : '' }}>
                        <a href="/findMovie?search={{ $search }}&type={{ $type }}&page={{ $ii }}">{{ $ii }}</a>
                    </li>
                @endfor
                <li class="disabled"><a href="#">...</a></li>
                <li>
                    <a href="/findMovie?search={{ $search }}&type={{ $type }}&page={{ $numPages }}">{{ $numPages }}</a>
                </li>
            @elseif($results['page'] >= $numPages - 5 )
                <li>
                    <a href="/findMovie?search={{ $search }}&type={{ $type }}&page=1">1</a>
                </li>
                <li class="disabled"><a href="#">...</a></li>
                @for($ii = $numPages - 9;$ii <= $numPages;$ii++)
                    <li{{ ($results['page']==$ii) ? ' class=active' : '' }}>
                        <a href="/findMovie?search={{ $search }}&type={{ $type }}&page={{ $ii }}">{{ $ii }}</a>
                    </li>
                @endfor
            @else
                <li>
                    <a href="/findMovie?search={{ $search }}&type={{ $type }}&page=1">1</a>
                </li>
                <li class="disabled"><a href="#">...</a></li>

                @for($ii = $results['page'] - 4; $ii <= $results['page'] + 4; $ii++)
                    <li{{ ($results['page']==$ii) ? ' class=active' : '' }}>
                        <a href="/findMovie?search={{ $search }}&type={{ $type }}&page={{ $ii }}">{{ $ii }}</a>
                    </li>
                @endfor
                <li class="disabled"><a href="#">...</a></li>
                <li>
                    <a href="/findMovie?search={{ $search }}&type={{ $type }}&page={{ $numPages }}">{{ $numPages }}</a>
                </li>
            @endif

            {{-- Show next page link --}}
            @if($results['page'] < $numPages)
                <li>
                    <a href="/findMovie?search={{ $search }}&type={{ $type }}&page={{ $results['page'] + 1 }}">next</a>
                </li>
            @endif
        </ul>
    </div>
@endif            