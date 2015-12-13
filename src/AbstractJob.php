<?php
namespace CDavison\Queue;

use CDavison\Queue\JobStatus;

abstract class AbstractJob {
    /**
     * @var int $attempts The number of attempts.
     */
    protected $attempts;

    /**
     * @var mixed $payload
     */
    protected $payload;

    /**
     * Construct a job for a payload.
     * 
     * @param mixed $payload Some payload that this worker can attempt to
     * process.
     */
    public function __construct($payload)
    {
        $this->setPayload($payload);
        $this->setStatus(JobStatus::NONE);
    }

    /**
     * Retrieve the payload of this job.
     * 
     * @return mixed
     */
    public abstract function getPayload();

    /**
     * Set the payload of this job.
     *
     * @param mixed $payload Some payload that this worker can attempt to
     * process.
     */
    protected abstract function setPayload($payload);

    /**
     * Get the status of this worker.
     * 
     * @return int
     */
    protected abstract function getStatus();

    /**
     * Set the status of this worker.
     *
     * @param int $status
     * @throws \DomainException If the job status is invalid.
     * @see JobStatus For an enumeration of valid statuses.
     */
    protected abstract function setStatus($status);

    /**
     * @return boolean True if this job has completed.
     */
    public function hasCompleted()
    {
        return $this->getStatus() === JobStatus::COMPLETED;
    }

    /**
     * @return boolean True if this job has failed.
     */
    public function hasFailed()
    {
        return $this->getStatus() === JobStatus::FAILED;
    }

    /**
     * @return boolean True if this job has finished.
     */
    public function hasFinished()
    {
        $status = $this->getStatus();

        return JobStatus::COMPLETED === $status
            || JobStatus::FAILED === $status;
    }

    /**
     * @return boolean True if this job has started.
     */
    public function hasStarted()
    {
        $status = $this->getStatus();

        return JobStatus::NONE !== $status && JobStatus::WAITING !== $status;
    }

    /**
     * @return boolean True if this job is sleeping.
     */
    public function isSleeping()
    {
        return JobStatus::SLEEPING === $this->getStatus();
    }

    /**
     * Mark this job as started.
     *
     * @return void
     */
    public function start()
    {
        $this->setStatus(JobStatus::RUNNING);
    }

    /**
     * Mark this job as sleeping.
     *
     * @return void
     */
    public function sleep()
    {
        $this->setStatus(JobStatus::SLEEPING);
    }

    /**
     * Mark this job as completed.
     *
     * @return void
     */
    public function complete()
    {
        $this->setStatus(JobStatus::COMPLETED);
    }

    /**
     * Mark this job as failed.
     *
     * @return void
     */
    public function fail()
    {
        $this->setStatus(JobStatus::FAILED);
    }
}