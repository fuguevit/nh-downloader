<?php

namespace Fuguevit\NHDownloader\Contract;

interface DownloadObserverContract
{
    public function handleSuccess($currentPage);
    
    public function handleFailed($currentPage);
}