<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Save Controller for global configuration
 *
 * @package     Joomla.Administrator
 * @subpackage  com_config
 * @since       3.2
*/
class ConfigControllerComponentSave extends JControllerBase
{
	/**
	 * Application object - Redeclared for proper typehinting
	 *
	 * @var    JApplicationCms
	 * @since  3.2
	 */
	protected $app;

	/**
	 * Method to save global configuration.
	 *
	 * @return  mixed  Calls $app->redirect()
	 *
	 * @since   3.2
	 */
	public function execute()
	{
		// Check for request forgeries.
		if (!JSession::checkToken())
		{
			$this->app->enqueueMessage(JText::_('JINVALID_TOKEN'));
			$this->app->redirect('index.php');
		}

		// Set FTP credentials, if given.
		JClientHelper::setCredentialsFromRequest('ftp');

		$model  = new ConfigModelComponent;
		$form   = $model->getForm();
		$data   = $this->input->get('jform', array(), 'array');
		$id     = $this->input->getInt('id');
		$option = $this->input->get('component');

		// Check if the user is authorized to do this.
		if (!JFactory::getUser()->authorise('core.admin', $option))
		{
			$this->app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'));
			$this->app->redirect('index.php');
		}

		$returnUri = $this->input->post->get('return', null, 'base64');

		$redirect = '';

		if (!empty($returnUri))
		{
			$redirect = '&return=' . urlencode($returnUri);
		}

		// Validate the posted data.
		$return = $model->validate($form, $data);

		// Check for validation errors.
		if ($return === false)
		{
			/*
			 * The validate method enqueued all messages for us, so we just need to redirect back.
			 */

			// Save the data in the session.
			$this->app->setUserState('com_config.config.global.data', $data);

			// Redirect back to the edit screen.
			$this->app->redirect(JRoute::_('index.php?option=com_config&view=component&component=' . $option . $redirect, false));
		}

		// Attempt to save the configuration.
		$data = array(
			'params' => $return,
			'id'     => $id,
			'option' => $option
		);

		try
		{
			$model->save($data);
		}
		catch (RuntimeException $e)
		{
			// Save the data in the session.
			$this->app->setUserState('com_config.config.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			$this->app->enqueueMessage(JText::sprintf('JERROR_SAVE_FAILED', $e->getMessage()), 'error');
			$this->app->redirect(JRoute::_('index.php?option=com_config&view=component&component=' . $option . $redirect, false));
		}

		// Set the redirect based on the task.
		switch ($this->options[3])
		{
			case 'apply':
				$this->app->enqueueMessage(JText::_('COM_CONFIG_SAVE_SUCCESS'));
				$this->app->redirect(JRoute::_('index.php?option=com_config&view=component&component=' . $option . $redirect, false));

				break;

			case 'save':
			default:
				$redirect = 'index.php?option=' . $option;

				if (!empty($returnUri))
				{
					$redirect = base64_decode($returnUri);
				}

				$this->app->redirect(JRoute::_($redirect, false));

				break;
		}

		return true;
	}
}