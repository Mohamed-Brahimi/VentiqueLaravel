<!-- <template>
    <div>
        <div class="antique">
            <header class="antique-header">
                <a href="{{ url('antiques/' . $antique->id) }}">
                    <h1 class="antique-nom">
                        {{ $antique->name }}
                    </h1>
                </a>

                @if ($antique->image)
                <div class="image">
                    <img
                        src="{{ asset('storage/' . $antique->image) }}"
                        alt="{{ $antique->name }}"
                    />
                </div>
                @endif

                <h3 class="antique-desc">{{ $antique->description }}</h3>
            </header>
            <p class="antique-prix">{{ $antique->price }}$</p>
        </div>
    </div>
</template>
<script></script> -->
