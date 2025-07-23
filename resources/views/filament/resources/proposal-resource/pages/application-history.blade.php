<x-filament::page
    :class="'apphistory'"
>

    <!--<span class="badge-green badge-yellow badge-red"></span>-->

    <ol class="relative mt-6">

        @php
            $reverse = false;
        @endphp


        @foreach ($this->record->getApplicationHistory('getViewData',[null,'application_history'], reverse: $reverse) as $statusWithActivities)
            @php
                $statusCode = \Illuminate\Support\Arr::get($statusWithActivities,'status_code');
                $statusColor = $this->record->getStatusColor($statusCode);
                $statusGroupColor = \Illuminate\Support\Arr::get($statusColor,'group','gray');
                $statusDescription = $this->record->fsm->getStateDescription($statusCode);
                $statusInfo = \Illuminate\Support\Arr::get($statusWithActivities,'info');
            @endphp

            <li class="mb-1 ms-4 list-none">


                <div class="flex w-full gap-y-1">
                    <div class="w-1/5">
                        <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                            {{\Illuminate\Support\Arr::get($statusWithActivities,'timestamp')}}
                        </time>
                    </div>


                    <div class="w-4/5">


                        <div>

                            <div class="flex">
                                <div class="w-1/8">

                                    <div
                                        class="relative w-3 h-3 rounded-full mt-1.5 -start-1.5 border border-white"
                                        style="background-color:{{\Illuminate\Support\Arr::get($statusColor,'color')}}"
                                    >
                                    </div>

                                </div>
                                <div class="w-7/8">
                                    <div class="mb-2">
                                        @if (
                                                ($reverse && $loop->last) ||
                                                (!$reverse && $loop->first)
                                                )
                                            The application has been created
                                        @else
                                            The application has been updated as

                                            <span
                                                class="badge badge-{{$statusGroupColor}} font-semibold text-sm ml-2">
                                            {{$statusDescription}}
                                        </span>
                                        @endif

                                        @if (\Illuminate\Support\Arr::get($statusInfo,'msg'))
                                            <div class="text-sm">
                                                {{$statusInfo['msg']}}
                                            </div>
                                        @endif
                                    </div>

                                </div>

                            </div>

                            @if (Auth::user()->hasPermissionTo('administer proposals'))

                            <div class="flex">

                                @php
                                    $statusActivities =  \Illuminate\Support\Arr::get($statusWithActivities,'activities');
                                    $statusActivitiesData =  \Illuminate\Support\Arr::get($statusWithActivities,'activities_data');
                                    $dottedClass = (!$loop->last || count($statusActivitiesData) > 0) ?
                                        "border-s border-dotted border-gray-400 dark:border-gray-700 min-h-[24px]" :
                                         '';
                                @endphp

                                    <div class="{{$dottedClass}}">

                                        @foreach ($statusActivitiesData as $activityKey => $statusActivityData)
                                            @php
                                                $statusActivity =  $statusActivities[$activityKey];
                                            @endphp
                                            @if ($loop->first)
                                                <ol>
                                                    @endif
                                                    @if($statusActivityData)
                                                        <li class="mb-1 ms-3 text-sm list-none">


                                                            <span class="italic text-blue-700">{{$statusActivity->getTimestamp()}}</span>
                                                            -
                                                            <span
                                                                class="font-semibold">{{$statusActivity->getName()}}</span>
                                                            <br/>
                                                            {!! $statusActivityData !!}
                                                        </li>
                                                    @endif

                                                    @if ($loop->last)
                                                </ol>
                                            @endif
                                        @endforeach

                                    </div>

                            </div>

                            @endif

                        </div>
                    </div>
                </div>
            </li>
        @endforeach
    </ol>

    <style>
        .apphistory section {
            row-gap: 0rem !important;
        }
    </style>

</x-filament::page>
