import os
import glob
import subprocess
from playwright.sync_api import sync_playwright
import imageio_ffmpeg

def record_demo1():
    video_dir = os.path.abspath("scratch/video_raw_1")
    os.makedirs(video_dir, exist_ok=True)
    os.makedirs("videos", exist_ok=True)
    
    # Clean previous raw videos
    for f in glob.glob(os.path.join(video_dir, "*")):
        try:
            os.remove(f)
        except:
            pass

    print("[Demo 1] Launching browser for recording...")
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(
            viewport={"width": 1920, "height": 1080},
            record_video_dir=video_dir,
            record_video_size={"width": 1920, "height": 1080}
        )
        page = context.new_page()

        # 1. Login
        print("[Demo 1] Navigating to Login...")
        page.goto("http://localhost:5001/Account/Login")
        page.wait_for_load_state("networkidle")
        page.wait_for_timeout(1000)

        page.click("input[id='Input.Email']")
        page.type("input[id='Input.Email']", "tonyclarke999@gmail.com", delay=40)
        page.wait_for_timeout(300)

        page.click("input[id='Input.Password']")
        page.type("input[id='Input.Password']", "Password1-", delay=40)
        page.wait_for_timeout(500)

        page.click("button[type='submit']")
        page.wait_for_timeout(2000)

        # 2. Dashboard overview
        print("[Demo 1] Showcasing Dashboard...")
        page.goto("http://localhost:5001/")
        page.wait_for_timeout(2500)

        # 3. Navigate to Projects
        print("[Demo 1] Navigating to Projects...")
        page.click("a[href='projects']")
        page.wait_for_timeout(2000)

        # 4. Click Create Test Project
        print("[Demo 1] Creating new Test Project...")
        page.click("a[href='/projects/create']")
        page.wait_for_timeout(1500)

        # 5. Fill project form
        page.click("#title")
        page.type("#title", "E-Commerce Checkout & Payment Flow", delay=45)
        page.wait_for_timeout(500)

        page.click("#jiraReference")
        page.type("#jiraReference", "PROJ-101", delay=50)
        page.wait_for_timeout(500)

        # Quill editor description
        quill = page.locator(".ql-editor")
        if quill.count() > 0:
            quill.click()
            quill.type(
                "End-to-end automated and manual test scenarios covering cart checkout, "
                "Stripe 3D-Secure card verification, coupon validations, and automated order confirmations.",
                delay=30
            )
        page.wait_for_timeout(1000)

        # 6. Submit Project
        page.click("button[type='submit']")
        page.wait_for_timeout(2500)

        # 7. Open Project Details
        print("[Demo 1] Inspecting created project...")
        # Find project row with our title
        row = page.locator("tr:has-text('E-Commerce Checkout & Payment Flow')")
        if row.count() > 0:
            row.locator("a[href*='/projects/details/']").first.click()
            page.wait_for_timeout(2000)
            
            # Hover over action buttons
            export_btn = page.locator("a:has-text('CSV'), button:has-text('CSV')")
            if export_btn.count() > 0:
                export_btn.first.hover()
                page.wait_for_timeout(1000)
        else:
            page.goto("http://localhost:5001/projects")
            page.wait_for_timeout(2000)

        page.wait_for_timeout(2500)
        context.close()
        browser.close()

    # Convert recorded webm to mp4
    raw_videos = glob.glob(os.path.join(video_dir, "*.webm"))
    if not raw_videos:
        print("[Demo 1] Error: No recorded webm found!")
        return False

    raw_webm = raw_videos[0]
    out_mp4 = os.path.abspath("videos/demo1_project_and_jira.mp4")
    ffmpeg_exe = imageio_ffmpeg.get_ffmpeg_exe()

    print(f"[Demo 1] Converting {raw_webm} to {out_mp4} using FFmpeg...")
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
    print(f"[Demo 1] Successfully generated: {out_mp4} ({os.path.getsize(out_mp4)} bytes)")
    return True

if __name__ == "__main__":
    record_demo1()
