<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMeterUpdateLogsRequest;
use App\Http\Requests\UpdateMeterUpdateLogsRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\MeterUpdateLogsRepository;
use Illuminate\Http\Request;
use Flash;

class MeterUpdateLogsController extends AppBaseController
{
    /** @var MeterUpdateLogsRepository $meterUpdateLogsRepository*/
    private $meterUpdateLogsRepository;

    public function __construct(MeterUpdateLogsRepository $meterUpdateLogsRepo)
    {
        $this->middleware('auth');
        $this->meterUpdateLogsRepository = $meterUpdateLogsRepo;
    }

    /**
     * Display a listing of the MeterUpdateLogs.
     */
    public function index(Request $request)
    {
        $meterUpdateLogs = $this->meterUpdateLogsRepository->paginate(10);

        return view('meter_update_logs.index')
            ->with('meterUpdateLogs', $meterUpdateLogs);
    }

    /**
     * Show the form for creating a new MeterUpdateLogs.
     */
    public function create()
    {
        return view('meter_update_logs.create');
    }

    /**
     * Store a newly created MeterUpdateLogs in storage.
     */
    public function store(CreateMeterUpdateLogsRequest $request)
    {
        $input = $request->all();

        $meterUpdateLogs = $this->meterUpdateLogsRepository->create($input);

        Flash::success('Meter Update Logs saved successfully.');

        return redirect(route('meterUpdateLogs.index'));
    }

    /**
     * Display the specified MeterUpdateLogs.
     */
    public function show($id)
    {
        $meterUpdateLogs = $this->meterUpdateLogsRepository->find($id);

        if (empty($meterUpdateLogs)) {
            Flash::error('Meter Update Logs not found');

            return redirect(route('meterUpdateLogs.index'));
        }

        return view('meter_update_logs.show')->with('meterUpdateLogs', $meterUpdateLogs);
    }

    /**
     * Show the form for editing the specified MeterUpdateLogs.
     */
    public function edit($id)
    {
        $meterUpdateLogs = $this->meterUpdateLogsRepository->find($id);

        if (empty($meterUpdateLogs)) {
            Flash::error('Meter Update Logs not found');

            return redirect(route('meterUpdateLogs.index'));
        }

        return view('meter_update_logs.edit')->with('meterUpdateLogs', $meterUpdateLogs);
    }

    /**
     * Update the specified MeterUpdateLogs in storage.
     */
    public function update($id, UpdateMeterUpdateLogsRequest $request)
    {
        $meterUpdateLogs = $this->meterUpdateLogsRepository->find($id);

        if (empty($meterUpdateLogs)) {
            Flash::error('Meter Update Logs not found');

            return redirect(route('meterUpdateLogs.index'));
        }

        $meterUpdateLogs = $this->meterUpdateLogsRepository->update($request->all(), $id);

        Flash::success('Meter Update Logs updated successfully.');

        return redirect(route('meterUpdateLogs.index'));
    }

    /**
     * Remove the specified MeterUpdateLogs from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $meterUpdateLogs = $this->meterUpdateLogsRepository->find($id);

        if (empty($meterUpdateLogs)) {
            Flash::error('Meter Update Logs not found');

            return redirect(route('meterUpdateLogs.index'));
        }

        $this->meterUpdateLogsRepository->delete($id);

        Flash::success('Meter Update Logs deleted successfully.');

        return redirect(route('meterUpdateLogs.index'));
    }
}
