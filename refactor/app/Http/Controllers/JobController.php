<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\JobRepository;
use Exception;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class JobController extends Controller
{

    /**
     * @var JobRepository
     */
    protected $repository;

    /**
     * JobController constructor.
     * @param JobRepository $jobRepository
     */
    public function __construct(JobRepository $jobRepository)
    {
        $this->repository = $jobRepository;
    }

    /**
     * Get a response based on the user's role and optional user ID.
     *
     * @param Request $request The HTTP request object.
     * @return JsonResponse The JSON response containing the requested data.
     */
    public function index(Request $request)
    {

        try {
            $response = [];

            $user = $request->__authenticatedUser;

            if ($user_id = $request->get('user_id')) {
                $response = $this->repository->getUsersJobs($user_id);
            } else if (
                $user->user_type == Config::get('constants.ADMIN_ROLE_ID') ||
                $user->user_type == Config::get('constants.SUPERADMIN_ROLE_ID')
            ) {
                $response = $this->repository->getAll($request);
            }

            return response($response);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the details of a specific job.
     *
     * @param int $id The ID of the job.
     * @return JsonResponse The JSON response containing the job details or an error message.
     */
    public function show($id)
    {
        try {
            $job = $this->repository->with('translatorJobRel.user')->find($id);

            // Return a JSON response with the job details.
            return response($job);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store job
     *
     * @param Request $request The HTTP request object containing the job data to be stored.
     * @return mixed The JSON response containing the result of the store operation or validation errors.
     */
    public function store(Request $request)
    {
        try {
            $response = $this->repository->store($request->__authenticatedUser, $request->all());

            // Return a JSON response with the result of the store operation.
            return response($response);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Update a specific job record.
     *
     * @param int $id The ID of the job to update.
     * @param Request $request The HTTP request object containing the updated data.
     * @return mixed The JSON response indicating the result of the update or an error message.
     */
    public function update($id, Request $request)
    {
        try {
            $response = $this->repository
                ->updateJob($id, array_except($request->all(), ['_token', 'submit']), $request->__authenticatedUser);

            return response($response);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Store job and send email based.
     *
     * @param Request $request The HTTP request object.
     * @return mixed The JSON response.
     */
    public function immediateJobEmail(Request $request)
    {
        try {
            $response = $this->repository->storeJobEmail($request->all());

            return response($response);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get the job history for a specific user if a user ID.
     *
     * @param Request $request The HTTP request object containing user ID.
     * @return mixed The JSON response containing the job history.
     */
    public function getHistory(Request $request)
    {
        try {
            // Check if a user ID is provided in the request.
            if ($request->user_id) {
                $response = $this->repository->getUsersJobsHistory($request->user_id, $request);

                return response($response);
            }

            // If no user ID is provided, return an appropriate error response.
            return response(['error' => 'User ID not provided.'], 400);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        try {
            $response = $this->repository->acceptJob($request->all(), $request->__authenticatedUser);

            return response($response);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    public function acceptJobWithId(Request $request)
    {
        try{
            $response = $this->repository->acceptJobWithId($request->job_id, $request->__authenticatedUser);

            return response($response);
        } catch(\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        try{
            $response = $this->repository->cancelJobAjax($request->all(), $request->__authenticatedUser);

            return response($response);
        } catch(\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        try{
            $response = $this->repository->endJob($request->all());

            return response($response);
        } catch(\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
        
    }

    public function customerNotCall(Request $request)
    {
        try{
            $response = $this->repository->customerNotCall($request->all());

            return response($response);
        } catch(\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
        
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        try{
            $response = $this->repository->getPotentialJobs($request->__authenticatedUser);

            return response($response);
        } catch(\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    public function distanceFeed(Request $request)
    {
        try{
            $updated = $this->repository->distanceFeed($request->all());

            if($updated) {
                return response('Record updated!');    
            }

            return response('Record could not be updated!');
        } catch(\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
        
    }

    public function reopen(Request $request)
    {
        try{
            $response = $this->repository->reopen($request->all());

            return response($response);
        } catch(\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }       
    }

    public function resendNotifications(Request $request)
    {
        try{
            $job = $this->repository->find($request->jobid);
            $job_data = $this->repository->jobToData($job);
            $this->repository->sendNotificationToTranslators($job, $job_data, '*');

            return response(['success' => 'Push sent']);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
        
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        try {
            $job = $this->repository->find($request->jobid);
        
            $this->repository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }
}
