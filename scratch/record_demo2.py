import os
import glob
import subprocess
import time
from playwright.sync_api import sync_playwright
import imageio_ffmpeg

def record_demo2():
    video_dir = os.path.abspath("scratch/video_raw_2")
    os.makedirs(video_dir, exist_ok=True)
    os.makedirs("videos", exist_ok=True)

    # Clean previous raw videos
    for f in glob.glob(os.path.join(video_dir, "*")):
        try:
            os.remove(f)
        except Exception:
            pass

    print("[Demo 2] Launching browser for recording Demo 2 (Execution & Bug Logging)...", flush=True)
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(
            viewport={"width": 1920, "height": 1080},
            record_video_dir=video_dir,
            record_video_size={"width": 1920, "height": 1080}
        )
        page = context.new_page()

        # 1. Login
        print("[Demo 2] Logging in...", flush=True)
        page.goto("http://localhost:5001/Account/Login")
        page.wait_for_load_state("networkidle")
        page.wait_for_timeout(1000)

        page.click("input[id='Input.Email']")
        page.type("input[id='Input.Email']", "tonyclarke999@gmail.com", delay=35)
        page.wait_for_timeout(300)

        page.click("input[id='Input.Password']")
        page.type("input[id='Input.Password']", "Password1-", delay=35)
        page.wait_for_timeout(400)

        page.click("button[type='submit']")
        page.wait_for_timeout(2500)

        # 2. Navigate to Projects
        print("[Demo 2] Navigating to Projects...", flush=True)
        page.goto("http://localhost:5001/projects")
        page.wait_for_timeout(2000)

        # Check for projects
        details_link = page.locator("a[href*='/projects/details/']").first
        if details_link.count() > 0:
            details_link.click()
            page.wait_for_timeout(2000)
        else:
            print("[Demo 2] No project found, creating one...", flush=True)
            page.goto("http://localhost:5001/projects/create")
            page.wait_for_timeout(1000)
            page.type("#title", "Core Platform Regression")
            page.type("#jiraReference", "PROJ-200")
            page.click("button[type='submit']")
            page.wait_for_timeout(2000)
            page.locator("a[href*='/projects/details/']").first.click()
            page.wait_for_timeout(1500)

        current_url = page.url
        print(f"[Demo 2] On Project Details page: {current_url}", flush=True)

        # Check if Start Test Run button exists
        start_run_btn = page.locator("a[href*='/testruns/setup']")
        if start_run_btn.count() == 0 or not start_run_btn.is_visible():
            print("[Demo 2] Creating a Test Suite first...", flush=True)
            # Find projectId from URL
            proj_id = current_url.rstrip("/").split("/")[-1]
            page.goto(f"http://localhost:5001/testsuites/create?projectId={proj_id}")
            page.wait_for_timeout(1500)
            page.type("input.form-control", "Order Fulfillment & Payments")
            page.click("button[type='submit']")
            page.wait_for_timeout(2000)
            page.goto(f"http://localhost:5001/projects/details/{proj_id}")
            page.wait_for_timeout(1500)

        # 3. Start Test Run Setup
        print("[Demo 2] Starting Test Run Setup...", flush=True)
        start_run_btn = page.locator("a[href*='/testruns/setup']")
        if start_run_btn.count() > 0:
            start_run_btn.first.click()
        else:
            proj_id = current_url.rstrip("/").split("/")[-1]
            page.goto(f"http://localhost:5001/testruns/setup?projectId={proj_id}")
        page.wait_for_timeout(2000)

        # In Setup: select test suite if needed
        suite_select = page.locator("#suiteSelect")
        if suite_select.count() > 0:
            options = suite_select.locator("option").all()
            if len(options) > 1:
                val = options[1].get_attribute("value")
                if val and val != "0":
                    suite_select.select_option(val)
                    page.wait_for_timeout(1500)

        # Select all test cases
        select_all_btn = page.locator("button:has-text('Select All')")
        if select_all_btn.count() > 0:
            select_all_btn.click()
            page.wait_for_timeout(800)

        # Start execution
        start_exec_btn = page.locator("button:has-text('Start Test Run'), button:has-text('Start Run'), button.btn-success")
        if start_exec_btn.count() > 0:
            start_exec_btn.first.click()
            page.wait_for_timeout(2500)

        print(f"[Demo 2] Navigated to execution: {page.url}", flush=True)

        # 4. Step 1: PASS
        print("[Demo 2] Executing Step 1 -> PASS...", flush=True)
        page.wait_for_timeout(1500)
        pass_btn = page.locator("button:has-text('PASS')")
        if pass_btn.count() > 0:
            pass_btn.first.click()
            page.wait_for_timeout(1200)

        # Advance to next step
        next_step_btn = page.locator("button:has-text('Next Step')")
        if next_step_btn.count() > 0 and next_step_btn.is_enabled():
            next_step_btn.click()
            page.wait_for_timeout(1500)

        # 5. Step 2: FAIL & Report Bug
        print("[Demo 2] Executing Step 2 -> FAIL...", flush=True)
        fail_btn = page.locator("button:has-text('FAIL')")
        if fail_btn.count() > 0:
            fail_btn.first.click()
            page.wait_for_timeout(1000)

        notes_textarea = page.locator("#actualResult")
        if notes_textarea.count() > 0:
            notes_textarea.fill("Stripe 3DS payment challenge returned HTTP 500 Internal Server Error.")
            page.wait_for_timeout(1000)

        # Click Report Bug
        print("[Demo 2] Opening 1-Click Bug Reporting Modal...", flush=True)
        report_bug_btn = page.locator("button:has-text('Report Bug')")
        if report_bug_btn.count() > 0:
            report_bug_btn.first.click()
            page.wait_for_timeout(1500)

            # Fill Bug Modal
            title_input = page.locator(".modal-dialog input.form-control").first
            if title_input.count() > 0:
                title_input.fill("Payment Gateway Timeout during 3DS Challenge")
                page.wait_for_timeout(500)

            desc_input = page.locator(".modal-dialog textarea.form-control").first
            if desc_input.count() > 0:
                desc_input.fill(
                    "Steps to Reproduce:\n"
                    "1. Add items to cart and proceed to checkout\n"
                    "2. Submit test Visa card requiring 3D-Secure\n"
                    "3. Complete OTP verification in simulator\n"
                    "Expected: Order confirmed with HTTP 200\n"
                    "Actual: Unhandled exception HTTP 500 in PaymentWebhookController"
                )
                page.wait_for_timeout(1000)

            # Save bug
            save_bug_btn = page.locator(".modal-dialog button.btn-warning:has-text('Save')")
            if save_bug_btn.count() > 0:
                save_bug_btn.click()
                page.wait_for_timeout(1500)

            # Close modal if still visible
            close_btn = page.locator(".modal-dialog button:has-text('Close'), .modal-dialog button.btn-close")
            if close_btn.count() > 0 and close_btn.first.is_visible():
                close_btn.first.click()
                page.wait_for_timeout(1000)

        # 6. Showcase Traceability in Bugs & Audit Log
        print("[Demo 2] Navigating to Defect Management (/bugs)...", flush=True)
        page.goto("http://localhost:5001/bugs")
        page.wait_for_timeout(2500)

        print("[Demo 2] Navigating to Real-Time Audit Log (/admin/audit-report)...", flush=True)
        page.goto("http://localhost:5001/admin/audit-report")
        page.wait_for_timeout(3000)

        print("[Demo 2] Returning to Dashboard overview...", flush=True)
        page.goto("http://localhost:5001/")
        page.wait_for_timeout(2500)

        context.close()
        browser.close()

    # Convert recorded webm to mp4
    raw_videos = glob.glob(os.path.join(video_dir, "*.webm"))
    if not raw_videos:
        print("[Demo 2] Error: No recorded webm found!", flush=True)
        return False

    raw_webm = raw_videos[0]
    out_mp4 = os.path.abspath("videos/demo2_testrun_and_bug.mp4")
    ffmpeg_exe = imageio_ffmpeg.get_ffmpeg_exe()

    print(f"[Demo 2] Converting {raw_webm} to {out_mp4} using FFmpeg...", flush=True)
    cmd = [
        ffmpeg_exe, "-y",
        "-i", raw_webm,
        "-c:v", "libx264",
        "-preset", "slow",
        "-crf", "18",
        "-pix_fmt", "yuv420p",
        out_mp4
    ]
    subprocess.run(cmd, check=True)
    print(f"[Demo 2] Successfully generated: {out_mp4} ({os.path.getsize(out_mp4)} bytes)", flush=True)
    return True

if __name__ == "__main__":
    record_demo2()
