<?php

namespace Brightside\FormPdf\Task;

use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Scheduler\Task\AbstractTask;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Scheduler\AbstractAdditionalFieldProvider;

class CleanerFieldProvider extends AbstractAdditionalFieldProvider
{
    /**
     * Render additional information fields within the scheduler backend.
     *
     * @param array $taskInfo Array information of task to return
     * @param CleanerTask $task Task object
     * @param SchedulerModuleController $schedulerModule Reference to the BE module of the Scheduler
     * @return array Additional fields
     * @see \TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface->getAdditionalFields($taskInfo, $task, $schedulerModule)
     */
    public function getAdditionalFields(array &$taskInfo, $task, SchedulerModuleController $schedulerModule)
    {
        $additionalFields = array();

        /** @var CleanerTask $task */

        if (empty($taskInfo['days'])) {
            if ($schedulerModule->getCurrentAction() == 'add') {
                $taskInfo['days'] = '5';
            } elseif ($schedulerModule->getCurrentAction() == 'edit') {
                $taskInfo['days'] = $task->getDays();
            } else {
                $taskInfo['days'] = $task->getDays();
            }
        }

        $fieldId = 'task_days';
        $fieldName = 'tx_scheduler[cleaner][days]';
        $fieldCode = '<input type="text"  name="' . $fieldName . '" id="' . $fieldId . '" value="' .
            htmlspecialchars($taskInfo['days']) . '" />';

        $label = $this->getLanguageService()->sL('LLL:EXT:form_pdf/Resources/Private/Language/locallang.xlf:form_pdf.tasks.cleaner.days');
        $label = BackendUtility::wrapInHelp('grabber', $fieldId, $label);
        $additionalFields[$fieldId] = array(
            'code' => $fieldCode,
            'label' => $label
        );

        return $additionalFields;
    }

    /**
     * This method checks any additional data that is relevant to the specific task.
     * If the task class is not relevant, the method is expected to return TRUE.
     *
     * @param array $submittedData Reference to the array containing the data submitted by the user
     * @param SchedulerModuleController $schedulerModule Reference to the BE module of the Scheduler
     * @return boolean TRUE if validation was ok (or selected class is not relevant), FALSE otherwise
     */
    public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $schedulerModule)
    {
        $isValid = TRUE;

        if (!$submittedData['cleaner']['days']) {
            $isValid = FALSE;
            $this->addMessage(
                $this->getLanguageService()->sL('LLL:EXT:form_pdf/Resources/Private/Language/locallang.xlf:form_pdf.tasks.cleaner.empty.days'),
                ContextualFeedbackSeverity::ERROR
            );
        }

        return $isValid;
    }

    /**
     * This method is used to save any additional input into the current task object
     * if the task class matches.
     *
     * @param array $submittedData Array containing the data submitted by the user
     * @param AbstractTask $task Reference to the current task object
     * @return void
     */
    public function saveAdditionalFields(array $submittedData, AbstractTask $task)
    {
        /** @var CleanerTask $task */
        $task->setDays($submittedData['cleaner']['days']);
    }

    /**
     * @return LanguageService|null
     */
    protected function getLanguageService(): ?LanguageService
    {
        return $GLOBALS['LANG'] ?? null;
    }
}
