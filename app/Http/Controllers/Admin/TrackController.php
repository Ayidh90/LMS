<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Models\Program;
use App\Http\Requests\StoreTrackRequest;
use App\Http\Requests\UpdateTrackRequest;
use App\Services\TrackService;
use App\Services\ProgramService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrackController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private TrackService $trackService,
        private ProgramService $programService
    ) {}

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'program_id' => $request->input('program_id'),
            'is_active' => $request->input('is_active'),
            'sort' => $request->input('sort', 'order'),
        ];

        $tracks = $this->trackService->getPaginatedTracks($filters, self::PER_PAGE);
        $programs = $this->programService->getPaginatedPrograms([], 100)->items();

        return Inertia::render('Admin/Tracks/Index', [
            'tracks' => $tracks,
            'programs' => $programs,
            'filters' => $request->only(['search', 'program_id', 'is_active', 'sort']),
        ]);
    }

    public function create(Request $request)
    {
        $programs = $this->programService->getPaginatedPrograms([], 100)->items();
        $selectedProgramId = $request->input('program_id');

        return Inertia::render('Admin/Tracks/Create', [
            'programs' => $programs,
            'selectedProgramId' => $selectedProgramId,
        ]);
    }

    public function store(StoreTrackRequest $request)
    {
        $track = $this->trackService->createTrack(
            $request->validated(),
            $request->file('thumbnail')
        );

        return redirect()
            ->route('admin.tracks.index')
            ->with('success', __('Track created successfully.'));
    }

    public function show(Track $track)
    {
        $track = $this->trackService->getTrackById($track->id);
        
        return Inertia::render('Admin/Tracks/Show', [
            'track' => $track,
        ]);
    }

    public function edit(Track $track)
    {
        $track = $this->trackService->getTrackById($track->id);
        $programs = $this->programService->getPaginatedPrograms([], 100)->items();
        
        return Inertia::render('Admin/Tracks/Edit', [
            'track' => $track,
            'programs' => $programs,
        ]);
    }

    public function update(UpdateTrackRequest $request, Track $track)
    {
        $this->trackService->updateTrack(
            $track,
            $request->validated(),
            $request->file('thumbnail')
        );

        return redirect()
            ->route('admin.tracks.index')
            ->with('success', __('Track updated successfully.'));
    }

    public function destroy(Track $track)
    {
        $this->trackService->deleteTrack($track);

        return redirect()
            ->route('admin.tracks.index')
            ->with('success', __('Track deleted successfully.'));
    }
}

