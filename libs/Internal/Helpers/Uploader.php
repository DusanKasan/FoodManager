<?php
/**
 * @todo: premenovat na uploaded_files_model
 * @todo: v konstruktore nastavit z neonu max image width/height
 * 
 * Handling file uploads:
 * - move file to uploads directory
 * - create hash and move to right directory
 * - Add info to database
 *
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class Uploader extends \BaseModel
{	
	/*
	 * @param Nette\Http\FileUpload $file
	 * @param Nette\Security\User $user
	 * 
	 * @return ActiveRow
	 * 
	 * @throws \Exception
	 */
	public function upload(\Nette\Http\FileUpload $file, Nette\Security\User $user = NULL)
	{
		$had_transaction = $this->database->inTransaction();
		
		try {
			if (!$had_transaction) {
				$this->database->beginTransaction();
			}
			
			$partial_file_data = array(
				'original_filename' => $file->getName(),
				'size' => $file->getSize(),
				'uploaded_at' => new \DateTime,
				'id_user' => empty($user) ? NULL : $user->id,
			);
			$file_row = $this->database->table('uploaded_files')->insert($partial_file_data);
			
			$filename_hash = hash('md5', $file->getName() . $file_row->id_file);
			$new_filename = $filename_hash . '_' . $file->getName();
			$destination = UPLOAD_DIR . '/' . substr($new_filename, 0, 2) . '/' . $new_filename;
			
			$file->move($destination);
			
			if ($file->isImage()) {
				$image = Nette\Image::fromFile($destination);
				
				$width = ($image->getWidth() > 1900) ? 1900 : NULL;
				$height = ($image->getHeight() > 1000) ? 1000 : NULL;
				if ($height != NULL || $width != NULL) {
					$image->resize($width, $height);
				}
				
				$image->resize($width, $height);
				$image->save($destination);
			}
			
			$update_data = array(
				'filename' => $destination,
			);
			$file_row->update($update_data);
			
			if (!$had_transaction) {
				$this->database->commit();
			}
			
			return $this->database->table('uploaded_files')->get($file_row->id_file);
		} catch (\Exception $exception) {
			if (!$had_transaction) {
				$this->database->rollBack();
			}
			
			throw $exception;
		}
	}
}

