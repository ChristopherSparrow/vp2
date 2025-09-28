@php
    $player = $player ?? null;
    $name = old('name', $player->name ?? '');
    $position = old('position', $player->position ?? '');
    $number = old('number', $player->number ?? '');
    $oldAssignments = old('assignments', null);
    $existingAssignments = $oldAssignments ?? ($player ? $player->playerTeams()->orderBy('start_date')->get()->map(function($pt){
        return [
            'id' => $pt->id,
            'team_id' => $pt->team_id,
            'start_date' => optional($pt->start_date)->toDateString(),
            'end_date' => optional($pt->end_date)->toDateString(),
        ];
    })->toArray() : []);
@endphp

<div class="grid grid-cols-1 gap-4">
    <div>
        <label class="block font-medium">Name</label>
        <input type="text" name="name" value="{{ $name }}" class="form-input mt-1 block w-full" required />
    </div>

    <div>
        <label class="block font-medium">Position</label>
        <input type="text" name="position" value="{{ $position }}" class="form-input mt-1 block w-full" />
    </div>

    <div>
        <label class="block font-medium">Number</label>
        <input type="number" name="number" value="{{ $number }}" class="form-input mt-1 block w-full" />
    </div>

    <div>
        <label class="block font-medium">Team Assignments</label>
        <div id="assignments-container" class="space-y-2 mt-2">
            {{-- Existing assignments will be rendered here by JS on load --}}
        </div>

        <div class="mt-2">
            <button type="button" id="add-assignment" class="btn btn-secondary">Add Assignment</button>
        </div>
    </div>
</div>

{{-- Template used by JS to create new assignment rows --}}
<template id="assignment-template">
    <div class="assignment-row grid grid-cols-12 gap-2 items-end p-2 border rounded">
        <div class="col-span-5">
            <label class="block text-sm">Team</label>
            <select name="__NAME__[team_id]" class="form-select mt-1 block w-full">
                <option value="">-- select team --</option>
                @foreach($teams as $team)
                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-3">
            <label class="block text-sm">Start Date</label>
            <input type="date" name="__NAME__[start_date]" class="form-input mt-1 block w-full" />
        </div>
        <div class="col-span-3">
            <label class="block text-sm">End Date</label>
            <input type="date" name="__NAME__[end_date]" class="form-input mt-1 block w-full" />
        </div>
        <div class="col-span-1 text-right">
            <button type="button" class="remove-assignment text-red-600">Remove</button>
        </div>
    </div>
</template>

<script>
    (function(){
        const container = document.getElementById('assignments-container');
        const tpl = document.getElementById('assignment-template').innerHTML;
        const addBtn = document.getElementById('add-assignment');

        let counter = 0;

        function nameFor(idx){
            return `assignments[${idx}]`;
        }

        function renderAssignment(data){
            const html = tpl.replace(/__NAME__/g, nameFor(counter));
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html;
            const row = wrapper.firstElementChild;

            // populate values if provided
            if(data){
                if(data.team_id) row.querySelector(`select[name="${nameFor(counter)}[team_id]"]`).value = data.team_id;
                if(data.start_date) row.querySelector(`input[name="${nameFor(counter)}[start_date]"]`).value = data.start_date;
                if(data.end_date) row.querySelector(`input[name="${nameFor(counter)}[end_date]"]`).value = data.end_date;
                // hidden id field if existing
                if(data.id){
                    const hid = document.createElement('input');
                    hid.type = 'hidden';
                    hid.name = `${nameFor(counter)}[id]`;
                    hid.value = data.id;
                    row.appendChild(hid);
                }
            }

            row.querySelector('.remove-assignment').addEventListener('click', function(){
                row.remove();
            });

            container.appendChild(row);
            counter++;
        }

        // initial render from server-provided assignments
        const existing = {!! json_encode($existingAssignments) !!};
        existing.forEach(a => renderAssignment(a));

        addBtn.addEventListener('click', function(){ renderAssignment({}); });
    })();
</script>
