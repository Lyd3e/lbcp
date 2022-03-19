<?php
/**
 * 雪花算法类库
 *
 * @author Raj Luo
 */

namespace Lyd3e\Lbcp\Arithmetic;

use Exception;

class SnowFlake
{
    const INITIALLY = 1638288000; // 时间起始标记点，作为基准，一般取系统的最近时间（一旦确定不能变动）

    const MACHINE_ID_BITS    = 2; // 机器标识位数
    const DATACENTER_ID_BITS = 1; // 数据中心标识位数
    const SEQUENCE_BITS      = 9; // 毫秒内自增位

    private $machineId; // 工作机器ID
    private $datacenterId; // 数据中心ID
    private $sequence; // 毫秒内序列

    private $maxMachineId    = -1 ^ (-1 << self::MACHINE_ID_BITS); // 机器ID最大值
    private $maxDatacenterId = -1 ^ (-1 << self::DATACENTER_ID_BITS); // 数据中心ID最大值

    private $machineIdShift     = self::SEQUENCE_BITS; // 机器ID偏左移位数
    private $datacenterIdShift  = self::SEQUENCE_BITS + self::MACHINE_ID_BITS; // 数据中心ID左移位数
    private $timestampLeftShift = self::SEQUENCE_BITS + self::MACHINE_ID_BITS + self::DATACENTER_ID_BITS; // 时间毫秒左移位数
    private $sequenceMask       = -1 ^ (-1 << self::SEQUENCE_BITS); // 生成序列的掩码

    private $lastTimestamp = -1; // 上次生产id时间戳

    /**
     * SnowFlake constructor.
     *
     * @param $machineId
     * @param $datacenterId
     * @param int $sequence
     * @throws Exception
     */
    public function __construct($machineId, $datacenterId, $sequence = 0)
    {
        if ($machineId > $this->maxMachineId || $machineId < 0) {
            throw new Exception("machine Id can't be greater than {$this->maxMachineId} or less than 0");
        }

        if ($datacenterId > $this->maxDatacenterId || $datacenterId < 0) {
            throw new Exception("datacenter Id can't be greater than {$this->maxDatacenterId} or less than 0");
        }

        $this->machineId    = $machineId;
        $this->datacenterId = $datacenterId;
        $this->sequence     = $sequence;
    }

    /**
     * 生成 id
     *
     * @return int
     * @throws Exception
     */
    public function createId()
    {
        $timestamp = $this->createTimestamp();

        if ($timestamp < $this->lastTimestamp) { //当产生的时间戳小于上次的生成的时间戳时，报错
            $diffTimestamp = bcsub($this->lastTimestamp, $timestamp);
            throw new Exception("Clock moved backwards.  Refusing to generate id for {$diffTimestamp} milliseconds");
        }

        if ($this->lastTimestamp == $timestamp) { //当生成的时间戳等于上次生成的时间戳的时候
            $this->sequence = ($this->sequence + 1) & $this->sequenceMask; //序列自增一次

            if (0 == $this->sequence) { //当序列为0时，重新生成最新的时间戳
                $timestamp = $this->createNextTimestamp($this->lastTimestamp);
            }
        } else { //当生成的时间戳不等于上次的生成的时间戳的时候，序列归0
            $this->sequence = 0;
        }

        $this->lastTimestamp = $timestamp;

        return (($timestamp - self::INITIALLY) << $this->timestampLeftShift) |
            ($this->datacenterId << $this->datacenterIdShift) |
            ($this->machineId << $this->machineIdShift) |
            $this->sequence;
    }

    /**
     * 生成一个大于等于 上次生成的时间戳 的时间戳
     *
     * @param $lastTimestamp
     * @return false|float
     */
    protected function createNextTimestamp($lastTimestamp)
    {
        $timestamp = $this->createTimestamp();
        while ($timestamp <= $lastTimestamp) {
            $timestamp = $this->createTimestamp();
        }

        return $timestamp;
    }

    /**
     * 生成毫秒级别的时间戳
     *
     * @return false|float
     */
    protected function createTimestamp()
    {
        return floor(microtime(true) * 1);
    }
}
