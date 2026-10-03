import os
import xml.etree.ElementTree as ET
import json
import uuid
import shutil
from datetime import datetime

def convert_trx_to_allure(results_dir='allure-results'):
    if not os.path.exists(results_dir):
        print(f"No results directory found at: {results_dir}")
        return

    # XML namespace for TRX
    ns = {'trx': 'http://microsoft.com/schemas/VisualStudio/TeamTest/2010'}

    def parse_iso_time(time_str):
        if not time_str:
            return int(datetime.now().timestamp() * 1000)
        
        # Clean timezone offset since python fromisoformat might not support some formats easily
        clean_str = time_str
        if '+' in time_str:
            clean_str = time_str.split('+')[0]
        elif '-' in time_str[10:]:
            clean_str = time_str.rsplit('-', 1)[0]
        
        try:
            dt = datetime.fromisoformat(clean_str)
            return int(dt.timestamp() * 1000)
        except Exception:
            return int(datetime.now().timestamp() * 1000)

    # Process each TRX file in the results directory
    for f in os.listdir(results_dir):
        if not f.endswith('.trx'):
            continue

        trx_path = os.path.join(results_dir, f)
        print(f"\n--- Processing TRX File: {f} ---")
        
        try:
            tree = ET.parse(trx_path)
            root = tree.getroot()
            
            # 1. Retrieve the deployment root (used for building precise attachment paths)
            run_dep_root = ""
            deployment_elem = root.find('.//trx:Deployment', ns)
            if deployment_elem is not None:
                run_dep_root = deployment_elem.get('runDeploymentRoot') or ""
                print(f"Deployment root directory: {run_dep_root}")
            
            # 2. Map all UnitTests definitions to match testId to class and test names
            test_definitions = {}
            for ut in root.findall('.//trx:UnitTest', ns):
                test_id = ut.get('id')
                tm = ut.find('.//trx:TestMethod', ns)
                if tm is not None and test_id:
                    test_definitions[test_id] = {
                        'className': tm.get('className') or "",
                        'name': tm.get('name') or ""
                    }
            
            # 3. Process test execution results
            results_processed = 0
            for utr in root.findall('.//trx:UnitTestResult', ns):
                test_id = utr.get('testId')
                exec_id = utr.get('executionId') or str(uuid.uuid4())
                test_name = utr.get('testName') or "Unknown Test"
                outcome = utr.get('outcome')
                
                # Map MSTest outcome to Allure statuses
                status = 'broken'
                if outcome == 'Passed':
                    status = 'passed'
                elif outcome == 'Failed':
                    status = 'failed'
                elif outcome in ['Timeout', 'Aborted']:
                    status = 'broken'
                elif outcome in ['Inconclusive', 'NotExecuted', 'Skipped', 'Pending']:
                    status = 'skipped'
                
                start_time = parse_iso_time(utr.get('startTime'))
                stop_time = parse_iso_time(utr.get('endTime'))
                
                class_name = ""
                full_name = test_name
                if test_id in test_definitions:
                    class_name = test_definitions[test_id]['className']
                    full_name = f"{class_name}.{test_name}" if class_name else test_name
                
                attachments = []

                # 4. Extract Console outputs (StdOut / StdErr) and save them as text attachments
                output_elem = utr.find('.//trx:Output', ns)
                if output_elem is not None:
                    stdout_elem = output_elem.find('.//trx:StdOut', ns)
                    if stdout_elem is not None and stdout_elem.text:
                        stdout_text = stdout_elem.text.strip()
                        if stdout_text:
                            stdout_file_name = f"{exec_id}-stdout.txt"
                            stdout_dest_path = os.path.join(results_dir, stdout_file_name)
                            with open(stdout_dest_path, 'w', encoding='utf-8') as sf:
                                sf.write(stdout_text)
                            attachments.append({
                                'name': 'Standard Output',
                                'source': stdout_file_name,
                                'type': 'text/plain'
                            })
                            print(f"  Captured stdout log -> {stdout_file_name}")

                    stderr_elem = output_elem.find('.//trx:StdErr', ns)
                    if stderr_elem is not None and stderr_elem.text:
                        stderr_text = stderr_elem.text.strip()
                        if stderr_text:
                            stderr_file_name = f"{exec_id}-stderr.txt"
                            stderr_dest_path = os.path.join(results_dir, stderr_file_name)
                            with open(stderr_dest_path, 'w', encoding='utf-8') as sf:
                                sf.write(stderr_text)
                            attachments.append({
                                'name': 'Standard Error',
                                'source': stderr_file_name,
                                'type': 'text/plain'
                            })
                            print(f"  Captured stderr log -> {stderr_file_name}")

                # 5. Extract and map file attachments (Screenshots, traces)
                res_files = utr.find('.//trx:ResultFiles', ns)
                if res_files is not None:
                    for rf in res_files.findall('.//trx:ResultFile', ns):
                        rel_path = rf.get('path')
                        if not rel_path:
                            continue
                        
                        clean_rel = rel_path.replace('\\', '/')
                        file_name = os.path.basename(clean_rel)
                        
                        # Primary: Try resolving via the exact MSTest standard path structure
                        exact_source = None
                        if run_dep_root:
                            exact_source = os.path.join(results_dir, run_dep_root, 'In', exec_id, clean_rel)
                            if not os.path.exists(exact_source):
                                exact_source = None
                        
                        # Fallback: Fall back to a recursive walk search in the results directory
                        found_path = exact_source
                        if not found_path:
                            for r, d, files in os.walk(results_dir):
                                if file_name in files:
                                    found_path = os.path.join(r, file_name)
                                    break
                        
                        if found_path:
                            # Prepend the unique execution ID to prevent naming collisions
                            unique_dest_name = f"{exec_id}-{file_name}"
                            dest_path = os.path.join(results_dir, unique_dest_name)
                            
                            try:
                                shutil.copy2(found_path, dest_path)
                                
                                mime_type = 'application/octet-stream'
                                if file_name.endswith('.zip'):
                                    mime_type = 'application/zip'
                                elif file_name.endswith('.png'):
                                    mime_type = 'image/png'
                                elif file_name.endswith('.xml'):
                                    mime_type = 'application/xml'
                                elif file_name.endswith('.json'):
                                    mime_type = 'application/json'
                                elif file_name.endswith('.txt'):
                                    mime_type = 'text/plain'
                                
                                attachments.append({
                                    'name': 'trace' if file_name.endswith('.zip') else ('screenshot' if file_name.endswith('.png') else file_name),
                                    'source': unique_dest_name,
                                    'type': mime_type
                                })
                                print(f"  Mapped attachment precisely: {file_name} -> {unique_dest_name}")
                            except Exception as copy_err:
                                print(f"  Failed to copy attachment {file_name}: {copy_err}")
                        else:
                            print(f"  Warning: Attachment file not found anywhere: {file_name}")

                # 6. Build the Allure JSON result structure
                allure_result = {
                    "uuid": exec_id,
                    "historyId": full_name,
                    "fullName": full_name,
                    "name": test_name,
                    "status": status,
                    "stage": "finished",
                    "steps": [],
                    "attachments": attachments,
                    "parameters": [],
                    "start": start_time,
                    "stop": stop_time,
                    "labels": [
                        {"name": "suite", "value": class_name},
                        {"name": "testClass", "value": class_name},
                        {"name": "package", "value": class_name}
                    ]
                }
                
                # 7. Extract status details / failure messages and stack traces
                err_info = utr.find('.//trx:ErrorInfo', ns)
                if err_info is not None:
                    msg_elem = err_info.find('.//trx:Message', ns)
                    trace_elem = err_info.find('.//trx:StackTrace', ns)
                    
                    status_details = {}
                    if msg_elem is not None and msg_elem.text:
                        status_details["message"] = msg_elem.text
                    if trace_elem is not None and trace_elem.text:
                        status_details["trace"] = trace_elem.text
                    
                    if status_details:
                        allure_result["statusDetails"] = status_details

                # Write individual test result JSON
                result_json_path = os.path.join(results_dir, f"{exec_id}-result.json")
                with open(result_json_path, 'w', encoding='utf-8') as rf:
                    json.dump(allure_result, rf, indent=2, ensure_ascii=False)
                
                results_processed += 1

            # Delete the TRX file after successful processing
            os.remove(trx_path)
            print(f"Successfully processed {results_processed} results and deleted TRX file: {trx_path}")

        except Exception as e:
            print(f"Failed to parse and convert TRX file {trx_path}: {e}")

if __name__ == '__main__':
    convert_trx_to_allure()
